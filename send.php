<?php
declare(strict_types=1);

header("Content-Type: text/plain; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  http_response_code(405);
  exit("Method Not Allowed");
}

if (!empty($_POST["website"] ?? "")) {
  http_response_code(200);
  exit("OK");
}

$to = "info@иисотрудники.рф";
$subject = "Новая заявка с сайта иисотрудники.рф";

$name = trim((string)($_POST["name"] ?? ""));
$phone = trim((string)($_POST["phone"] ?? ""));
$company = trim((string)($_POST["company"] ?? ""));
$business = trim((string)($_POST["business"] ?? ""));
$crm = trim((string)($_POST["crm"] ?? ""));
$processes = trim((string)($_POST["processes"] ?? ""));
$comment = trim((string)($_POST["comment"] ?? ""));
$consent = trim((string)($_POST["consent"] ?? ""));
$channelsRaw = $_POST["channels"] ?? [];

if ($name === "" || $phone === "" || $comment === "") {
  http_response_code(400);
  exit("Пожалуйста, заполните обязательные поля: имя, телефон и комментарий.");
}

if ($consent !== "yes") {
  http_response_code(400);
  exit("Необходимо согласие на обработку персональных данных.");
}

$channels = "";
if (is_array($channelsRaw)) {
  $channels = implode(", ", array_map(static fn($item) => trim((string)$item), $channelsRaw));
} elseif (is_string($channelsRaw)) {
  $channels = trim($channelsRaw);
}

$message = "Новая заявка с сайта\n\n"
  . "Имя: {$name}\n"
  . "Телефон: {$phone}\n"
  . "Компания: {$company}\n"
  . "Сфера бизнеса: {$business}\n"
  . "CRM: {$crm}\n"
  . "Процессы для автоматизации: {$processes}\n"
  . "Каналы: {$channels}\n"
  . "Комментарий: {$comment}\n"
  . "Согласие на обработку ПД: да\n";

$headers = [];
$headers[] = "From: no-reply@иисотрудники.рф";
$headers[] = "Reply-To: no-reply@иисотрудники.рф";
$headers[] = "Content-Type: text/plain; charset=UTF-8";

$sent = @mail($to, $subject, $message, implode("\r\n", $headers));

if (!$sent) {
  http_response_code(500);
  exit("Не удалось отправить заявку. Попробуйте позже.");
}

header("Location: /?sent=1#contact");
exit;
