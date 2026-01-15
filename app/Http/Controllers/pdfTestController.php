<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Gotenberg\Gotenberg;
use Gotenberg\Stream;
use Gotenberg\Modules\ChromiumPdf;
use Illuminate\Http\Request;

class pdfTestController extends Controller
{
    public function testPdf()
    {
        $html = view('pdf.test-invoice')->render();

        $request = ChromiumPdf::make()
            ->html(Stream::string('index.html', $html));

        $response = Gotenberg::send(
            $request,
            'http://127.0.0.1:8088'
        );

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
