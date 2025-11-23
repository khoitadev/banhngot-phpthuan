<?php

/**
 * Tập tin cấu hình chatbot.
 * Bạn có thể đặt API key trực tiếp tại đây nếu không muốn dùng biến môi trường.
 *
 * Cấu hình AI Provider:
 * - 'openai': Dùng OpenAI (GPT-4o-mini)
 * - 'gemini': Dùng Google Gemini
 */

// Chọn AI provider: 'openai' hoặc 'gemini'
define('CHATBOT_AI_PROVIDER', 'gemini');

// API Key cho OpenAI (nếu dùng OpenAI)
define('CHATBOT_OPENAI_API_KEY', '');

// API Key cho Gemini (nếu dùng Gemini)
// Lấy từ: https://aistudio.google.com/app/apikey
define('CHATBOT_GEMINI_API_KEY', 'AIzaSyA9a0joO-ZoDVeB065etu5K0iEuCGiOit0');
