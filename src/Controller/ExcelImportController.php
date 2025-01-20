<?php

namespace App\Controller;

use App\Models\Ads;
use Shuchkin\SimpleXLSX;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class ExcelImportController
{

    public function __construct(
    ) {
    }

    public function load(Request $req, Response $res)
    {
        $files = $req->getUploadedFiles();
        if (!array_key_exists('xlsx', $files)) {
            return $res->withStatus(400);
        }
        /**
         * @var \Slim\Psr7\UploadedFile
         */
        $xlsx = $files['xlsx'];
        if ($xlsx->getError() !== UPLOAD_ERR_OK) {
            return $res->withStatus(400, 'bad upload');
        }

        $parsed = SimpleXLSX::parse($xlsx->getFilePath());
        Ads::fromXlsx($parsed);

        $res->getBody()->write("ok");
        return $res;
    }
}