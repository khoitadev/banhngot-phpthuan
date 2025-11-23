<?php
// Cho phép file này được include từ file khác (ví dụ test_ai.php)
$isIncluded = (basename($_SERVER['PHP_SELF']) !== 'chatbot.php');

if (!$isIncluded) {
  header('Content-Type: application/json; charset=utf-8');

  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['reply' => 'Tôi chỉ có thể trả lời qua yêu cầu POST.']);
    exit;
  }
}

require_once __DIR__ . '/database/connect.php';

// Optional config file to store API key directly
$chatbotConfig = __DIR__ . '/config/chatbot.php';
if (file_exists($chatbotConfig)) {
  require_once $chatbotConfig;
}

$chatbotAiDebug = [];

// Chỉ chạy main code khi không được include
if (!$isIncluded) {
  $raw = file_get_contents('php://input');
  $data = json_decode($raw, true);
  $message = isset($data['message']) ? trim($data['message']) : '';
  $debugMode = isset($data['debug']) && $data['debug'] === true;

  if ($message === '') {
    echo json_encode(['reply' => 'Bạn hãy nhập câu hỏi để tôi có thể hỗ trợ nhé.']);
    exit;
  }

  $normalized = mb_strtolower($message, 'UTF-8');

  $intents = [
    [
      'keywords' => ['giờ', 'mở cửa'],
      'type' => 'static',
      'answer' => 'Tiệm mở cửa 08:00-20:30 từ thứ 2-6 và 10:00-16:30 cuối tuần.'
    ],
    [
      'keywords' => ['giao hàng', 'ship'],
      'type' => 'static',
      'answer' => 'Chúng tôi giao nội thành trong khoảng 60 phút, phí từ 25.000đ tuỳ khu vực.'
    ],
    [
      'keywords' => ['menu', 'loại bánh'],
      'type' => 'static',
      'answer' => 'Bạn có thể xem toàn bộ bánh tại trang Cửa hàng và dùng bộ lọc theo danh mục.'
    ],
    [
      'keywords' => ['địa chỉ', 'ở đâu'],
      'type' => 'static',
      'answer' => 'Tiệm ở 123 Đường Ngọt Ngào, Quận 1, TP.HCM.'
    ],
    [
      'keywords' => ['giá', 'bao nhiêu'],
      'type' => 'static',
      'answer' => 'Giá dao động 45.000đ - 320.000đ tuỳ kích cỡ; xem chi tiết ở từng sản phẩm.'
    ],
    [
      'keywords' => ['liên hệ', 'hotline'],
      'type' => 'static',
      'answer' => 'Hotline 0909 000 111 hoặc email support@banhngot.test.'
    ],
    [
      'keywords' => ['thanh toán', 'payment'],
      'type' => 'static',
      'answer' => 'Nhận tiền mặt khi giao, chuyển khoản và ví Momo.'
    ],
    [
      'keywords' => ['đắt nhất', 'giá cao nhất', 'mắc nhất'],
      'type' => 'expensive_product'
    ],
    [
      'keywords' => ['rẻ nhất', 'giá thấp nhất', 'rẻ'],
      'type' => 'cheap_product'
    ],
    [
      'keywords' => ['bánh', 'bán chạy'],
      'type' => 'best_seller'
    ],
    [
      'keywords' => ['top', 'sản phẩm'],
      'type' => 'best_seller'
    ],
    [
      'keywords' => ['còn hàng', 'có sẵn'],
      'type' => 'available_products'
    ],
  ];

  $reply = null;
  $intentType = null;

  // Cải thiện logic matching: chỉ cần 1 keyword match (thay vì tất cả)
  foreach ($intents as $intent) {
    $match = false;
    foreach ($intent['keywords'] as $keyword) {
      if (mb_strpos($normalized, $keyword, 0, 'UTF-8') !== false) {
        $match = true;
        break;
      }
    }
    if ($match) {
      $intentType = $intent['type'];
      $reply = $intent['answer'] ?? null;
      break;
    }
  }

  // Xử lý các intent từ database
  if ($intentType === 'best_seller') {
    $reply = getBestSellerAnswer($conn) ?? $reply;
  } elseif ($intentType === 'expensive_product') {
    $reply = getExpensiveProductAnswer($conn) ?? $reply;
  } elseif ($intentType === 'cheap_product') {
    $reply = getCheapProductAnswer($conn) ?? $reply;
  } elseif ($intentType === 'available_products') {
    $reply = getAvailableProductsAnswer($conn) ?? $reply;
  }

  $aiUsed = false;
  $debugInfo = [];

  if ($reply === null) {
    // Thử dùng AI nếu có API key
    $aiReply = getAiAnswer($message, $conn);
    if ($aiReply !== null) {
      $reply = $aiReply;
      $aiUsed = true;
      $debugInfo['ai_used'] = true;
    } else {
      $debugInfo['ai_used'] = false;
      $debugInfo['ai_reason'] = getenv('OPENAI_API_KEY') ? 'AI call failed' : 'No API key';
    }

    // Nếu không có AI, thử tìm câu trả lời thông minh từ database
    if ($reply === null) {
      $reply = getSmartFallbackAnswer($normalized, $conn);
      if ($reply !== null) {
        $debugInfo['fallback_used'] = true;
      }
    }

    // Cuối cùng mới dùng câu trả lời mặc định
    if ($reply === null) {
      $reply = 'Hiện tôi chưa có thông tin cho câu hỏi này, bạn có thể để lại lời nhắn ở trang Liên hệ nhé!';
    }
  }

  $response = ['reply' => $reply];

  // Thêm debug info nếu có yêu cầu debug
  if ($debugMode) {
    $response['debug'] = $debugInfo;
    $response['debug']['intent_type'] = $intentType ?? 'none';
    $response['debug']['api_key_set'] = resolveApiKey() !== null;
    $response['debug']['curl_available'] = function_exists('curl_version');
    $response['debug']['ai_diag'] = getLastAiDebug();
  }

  echo json_encode($response);

  mysqli_close($conn);
} // End if (!$isIncluded)

function getBestSellerAnswer(mysqli $conn): ?string
{
  $sql = "SELECT p.Name, SUM(od.Quantity) AS total_qty
            FROM orderdetails od
            JOIN products p ON p.ProductId = od.ProductId
            WHERE od.Status = 1
            GROUP BY p.ProductId
            ORDER BY total_qty DESC
            LIMIT 3";

  $result = mysqli_query($conn, $sql);

  if (!$result || mysqli_num_rows($result) === 0) {
    return null;
  }

  $topProducts = [];
  while ($row = mysqli_fetch_assoc($result)) {
    $topProducts[] = $row;
  }

  $best = $topProducts[0];
  $others = array_slice($topProducts, 1);
  $othersText = '';

  if (!empty($others)) {
    $names = array_map(fn($item) => $item['Name'], $others);
    $othersText = ' Các vị được yêu thích khác: ' . implode(', ', $names) . '.';
  }

  return sprintf(
    '%s đang là bánh bán chạy nhất với tổng %d phần đã bán.%s',
    $best['Name'],
    (int) $best['total_qty'],
    $othersText
  );
}

function getExpensiveProductAnswer(mysqli $conn): ?string
{
  $sql = "SELECT Name, SellPrice, Quantity
            FROM products
            WHERE Status = 1 AND is_accept = 1
            ORDER BY SellPrice DESC
            LIMIT 3";

  $result = mysqli_query($conn, $sql);

  if (!$result || mysqli_num_rows($result) === 0) {
    return null;
  }

  $products = [];
  while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
  }

  $mostExpensive = $products[0];
  $price = number_format($mostExpensive['SellPrice'], 0, ',', '.');
  $others = array_slice($products, 1);
  $othersText = '';

  if (!empty($others)) {
    $names = array_map(function ($item) {
      return $item['Name'] . ' (' . number_format($item['SellPrice'], 0, ',', '.') . 'đ)';
    }, $others);
    $othersText = ' Các bánh cao cấp khác: ' . implode(', ', $names) . '.';
  }

  return sprintf(
    'Bánh đắt nhất hiện tại là %s với giá %sđ. Bạn có thể xem chi tiết tại trang Cửa hàng.%s',
    $mostExpensive['Name'],
    $price,
    $othersText
  );
}

function getCheapProductAnswer(mysqli $conn): ?string
{
  $sql = "SELECT Name, SellPrice, Quantity
            FROM products
            WHERE Status = 1 AND is_accept = 1
            ORDER BY SellPrice ASC
            LIMIT 3";

  $result = mysqli_query($conn, $sql);

  if (!$result || mysqli_num_rows($result) === 0) {
    return null;
  }

  $products = [];
  while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
  }

  $cheapest = $products[0];
  $price = number_format($cheapest['SellPrice'], 0, ',', '.');
  $others = array_slice($products, 1);
  $othersText = '';

  if (!empty($others)) {
    $names = array_map(function ($item) {
      return $item['Name'] . ' (' . number_format($item['SellPrice'], 0, ',', '.') . 'đ)';
    }, $others);
    $othersText = ' Các bánh giá tốt khác: ' . implode(', ', $names) . '.';
  }

  return sprintf(
    'Bánh rẻ nhất hiện tại là %s với giá %sđ. Bạn có thể xem chi tiết tại trang Cửa hàng.%s',
    $cheapest['Name'],
    $price,
    $othersText
  );
}

function getAvailableProductsAnswer(mysqli $conn): ?string
{
  $sql = "SELECT COUNT(*) AS total, SUM(CASE WHEN Quantity > 0 THEN 1 ELSE 0 END) AS available
            FROM products
            WHERE Status = 1 AND is_accept = 1";

  $result = mysqli_query($conn, $sql);

  if (!$result) {
    return null;
  }

  $row = mysqli_fetch_assoc($result);
  $total = (int) $row['total'];
  $available = (int) $row['available'];

  if ($available === 0) {
    return 'Hiện tại chúng tôi đang hết hàng. Vui lòng quay lại sau!';
  }

  return sprintf(
    'Hiện tại chúng tôi có %d sản phẩm đang còn hàng trong tổng số %d sản phẩm. Bạn có thể xem chi tiết tại trang Cửa hàng.',
    $available,
    $total
  );
}

function getLastAiDebug(): array
{
  global $chatbotAiDebug;
  return $chatbotAiDebug ?? [];
}

function getSmartFallbackAnswer(string $normalized, mysqli $conn): ?string
{
  // Tìm kiếm sản phẩm dựa trên từ khóa trong câu hỏi
  $productKeywords = ['bánh', 'sản phẩm', 'món', 'cake', 'kem', 'mì', 'quy'];
  $hasProductKeyword = false;
  foreach ($productKeywords as $keyword) {
    if (mb_strpos($normalized, $keyword, 0, 'UTF-8') !== false) {
      $hasProductKeyword = true;
      break;
    }
  }

  if ($hasProductKeyword) {
    // Tìm sản phẩm có tên chứa từ khóa
    $words = explode(' ', $normalized);
    $searchTerms = array_filter($words, function ($word) use ($productKeywords) {
      return mb_strlen($word, 'UTF-8') > 2 && !in_array($word, $productKeywords);
    });

    if (!empty($searchTerms)) {
      $searchTerm = array_values($searchTerms)[0];
      $sql = "SELECT Name, SellPrice, Description
                    FROM products
                    WHERE Status = 1 AND is_accept = 1
                    AND (Name LIKE '%" . mysqli_real_escape_string($conn, $searchTerm) . "%'
                         OR Description LIKE '%" . mysqli_real_escape_string($conn, $searchTerm) . "%')
                    LIMIT 3";

      $result = mysqli_query($conn, $sql);
      if ($result && mysqli_num_rows($result) > 0) {
        $products = [];
        while ($row = mysqli_fetch_assoc($result)) {
          $products[] = $row;
        }

        if (count($products) === 1) {
          $p = $products[0];
          $price = number_format($p['SellPrice'], 0, ',', '.');
          return sprintf(
            'Chúng tôi có %s với giá %sđ. %s Bạn có thể xem chi tiết tại trang Cửa hàng.',
            $p['Name'],
            $price,
            mb_substr($p['Description'], 0, 100, 'UTF-8') . '...'
          );
        } else {
          $names = array_map(function ($p) {
            return $p['Name'] . ' (' . number_format($p['SellPrice'], 0, ',', '.') . 'đ)';
          }, $products);
          return 'Chúng tôi có các sản phẩm: ' . implode(', ', $names) . '. Bạn có thể xem chi tiết tại trang Cửa hàng.';
        }
      }
    }
  }

  return null;
}

function resolveApiKey(): ?string
{
  $provider = defined('CHATBOT_AI_PROVIDER') ? CHATBOT_AI_PROVIDER : 'openai';

  if ($provider === 'gemini') {
    // Ưu tiên dùng constant từ file config
    if (defined('CHATBOT_GEMINI_API_KEY') && CHATBOT_GEMINI_API_KEY !== '') {
      return CHATBOT_GEMINI_API_KEY;
    }
    // Nếu không có trong config, thử lấy từ biến môi trường
    $envKey = getenv('GEMINI_API_KEY');
    if (!empty($envKey)) {
      return $envKey;
    }
  } else {
    // OpenAI
    if (defined('CHATBOT_OPENAI_API_KEY') && CHATBOT_OPENAI_API_KEY !== '') {
      return CHATBOT_OPENAI_API_KEY;
    }
    $envKey = getenv('OPENAI_API_KEY');
    if (!empty($envKey)) {
      return $envKey;
    }
  }

  return null;
}

function getProvider(): string
{
  return defined('CHATBOT_AI_PROVIDER') ? CHATBOT_AI_PROVIDER : 'openai';
}

function getAiAnswer(string $question, mysqli $conn): ?string
{
  $provider = getProvider();

  if ($provider === 'gemini') {
    return getGeminiAnswer($question, $conn);
  } else {
    return getOpenAIAnswer($question, $conn);
  }
}

function getOpenAIAnswer(string $question, mysqli $conn): ?string
{
  global $chatbotAiDebug;
  $chatbotAiDebug = [
    'curl_error' => null,
    'http_status' => null,
    'response_excerpt' => null,
    'json_error' => null,
    'has_key' => resolveApiKey() !== null,
    'provider' => 'openai',
  ];

  $apiKey = resolveApiKey();
  if (!$apiKey) {
    return null;
  }

  $context = buildContextSummary($conn);

  $payload = [
    'model' => 'gpt-4o-mini',
    'temperature' => 0.4,
    'messages' => [
      [
        'role' => 'system',
        'content' => 'Bạn là trợ lý ảo của tiệm bánh Bánh Ngọt. Trả lời ngắn gọn, thân thiện bằng tiếng Việt dựa trên dữ kiện được cung cấp. Nếu không chắc chắn, hãy nói chưa rõ và gợi ý khách liên hệ thêm.'
      ],
      [
        'role' => 'user',
        'content' => "Thông tin cửa hàng:\n" . $context
      ],
      [
        'role' => 'user',
        'content' => $question
      ],
    ],
  ];

  $url = 'https://api.openai.com/v1/chat/completions';
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey,
  ]);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);

  $response = curl_exec($ch);
  $curlError = curl_error($ch);
  $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $chatbotAiDebug['http_status'] = $status ?: null;
  curl_close($ch);

  if ($response === false || !empty($curlError)) {
    $chatbotAiDebug['curl_error'] = $curlError;
    return null;
  }

  if ($status >= 400) {
    $chatbotAiDebug['response_excerpt'] = substr($response, 0, 500);
    return null;
  }

  $data = json_decode($response, true);
  if (json_last_error() !== JSON_ERROR_NONE) {
    $chatbotAiDebug['json_error'] = json_last_error_msg();
    return null;
  }

  return $data['choices'][0]['message']['content'] ?? null;
}

function getGeminiAnswer(string $question, mysqli $conn): ?string
{
  global $chatbotAiDebug;
  $chatbotAiDebug = [
    'curl_error' => null,
    'http_status' => null,
    'response_excerpt' => null,
    'json_error' => null,
    'has_key' => resolveApiKey() !== null,
    'provider' => 'gemini',
  ];

  $apiKey = resolveApiKey();
  if (!$apiKey) {
    return null;
  }

  $context = buildContextSummary($conn);

  // Gemini API format
  $prompt = "Bạn là trợ lý ảo của tiệm bánh Bánh Ngọt. Trả lời ngắn gọn, thân thiện bằng tiếng Việt dựa trên dữ kiện được cung cấp. Nếu không chắc chắn, hãy nói chưa rõ và gợi ý khách liên hệ thêm.\n\n";
  $prompt .= "Thông tin cửa hàng:\n" . $context . "\n\n";
  $prompt .= "Câu hỏi: " . $question;

  $payload = [
    'contents' => [
      [
        'parts' => [
          [
            'text' => $prompt
          ]
        ]
      ]
    ],
    'generationConfig' => [
      'temperature' => 0.4,
      'maxOutputTokens' => 1024,
    ]
  ];

  // Thử các model theo thứ tự (free tier thường hỗ trợ các model này)
  $models = [
    'v1/models/gemini-pro',
    'v1beta/models/gemini-1.5-flash-latest',
    'v1beta/models/gemini-pro',
    'v1/models/gemini-1.5-flash',
  ];

  $response = null;
  $curlError = null;
  $status = null;
  $modelPath = 'none';

  foreach ($models as $modelPath) {
    $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apiKey;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Nếu thành công (200) hoặc lỗi không phải 404, dừng lại
    if ($status == 200 || ($status >= 400 && $status != 404)) {
      break;
    }

    // Nếu 404, thử model tiếp theo
    if ($status == 404) {
      continue;
    }
  }

  $chatbotAiDebug['http_status'] = $status ?: null;
  $chatbotAiDebug['model_tried'] = $modelPath;

  if ($response === false || !empty($curlError)) {
    $chatbotAiDebug['curl_error'] = $curlError;
    return null;
  }

  if ($status >= 400) {
    $chatbotAiDebug['response_excerpt'] = substr($response, 0, 500);
    return null;
  }

  $data = json_decode($response, true);
  if (json_last_error() !== JSON_ERROR_NONE) {
    $chatbotAiDebug['json_error'] = json_last_error_msg();
    return null;
  }

  // Gemini response format
  if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
    return $data['candidates'][0]['content']['parts'][0]['text'];
  }

  return null;
}

function buildContextSummary(mysqli $conn): string
{
  $hours = "Giờ mở cửa: 08:00-20:30 (T2-6), 10:00-16:30 (T7-CN).\nĐịa chỉ: 123 Đường Ngọt Ngào, Quận 1, TP.HCM.\nDịch vụ: giao hàng nội thành 60 phút, thanh toán tiền mặt/chuyển khoản/Momo.";

  $sql = "SELECT p.Name, SUM(od.Quantity) AS total_qty
            FROM orderdetails od
            JOIN products p ON p.ProductId = od.ProductId
            WHERE od.Status = 1
            GROUP BY p.ProductId
            ORDER BY total_qty DESC
            LIMIT 5";

  $result = mysqli_query($conn, $sql);

  if (!$result || mysqli_num_rows($result) === 0) {
    return $hours;
  }

  $summary = [];
  while ($row = mysqli_fetch_assoc($result)) {
    $summary[] = sprintf('%s: %d phần', $row['Name'], (int) $row['total_qty']);
  }

  return $hours . "\nTop sản phẩm: " . implode(', ', $summary) . '.';
}
