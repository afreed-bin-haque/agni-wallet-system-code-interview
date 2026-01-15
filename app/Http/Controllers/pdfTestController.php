<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Gotenberg\Gotenberg;
use Gotenberg\Stream;
use Illuminate\Http\Request;

class pdfTestController extends Controller
{
    public function testPdf()
    {
        $html = view('pdf.test-invoice')->render();


        $gotenberg = Gotenberg::chromium('http://127.0.0.1:8088');

        $response = $gotenberg
            ->html(Stream::string('invoice.html', $html))
            ->pdf()
            ->request();

        return new Response(
            $response->getBody()->getContents(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="test-invoice.pdf"',
            ]
        );
    }
}
