<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Gotenberg\Gotenberg;
use Gotenberg\Stream;

class pdfTestController extends Controller
{
    public function testPdf()
    {
        $html = view('pdf.testinvoice', [
            'name' => 'Afreed Bin Haque',
            'amount' => 150.75,
            'date' => now()->format('Y-m-d'),
        ])->render();


        $request = Gotenberg::chromium('http://127.0.0.1:8088')
            ->pdf()
            ->html(Stream::string('index.html', $html));

        $response = Gotenberg::send($request);

        return new Response(
            $response->getBody()->getContents(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="testinvoice.pdf"',
            ]
        );
    }
}
