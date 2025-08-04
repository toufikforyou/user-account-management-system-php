<?php

namespace Controllers;

class ErrorHandler
{
    public static function handlerException(\Throwable $exception): void
    {
        http_response_code(500);
        echo json_encode([
            "code" => 1030,
            "success" => false,
            "result" => $exception->getMessage() . $exception->getFile() . $exception->getLine(),
            "lang" => $_SERVER['HTTP_X_LANG'] ?? "bn",
            "timestamp" => date("Y-m-d H:i:s", time())
        ]);
    }
}