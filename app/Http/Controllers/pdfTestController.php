<?php

namespace App\Http\Controllers;

use App\Models\WalletTransaction;
use Exception;
use Illuminate\Http\Response;
use Gotenberg\Gotenberg;
use Gotenberg\Stream;
use Illuminate\Support\Facades\Log;

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

    public function printStatement($wallet_trx_id)
    {
        try {
            $data = WalletTransaction::findOrFail($wallet_trx_id);

            $data->bkash_res = $data->bkash_res
                ? json_decode($data->bkash_res, true)
                : null;


            $html = view('pdf.testinvoice', [
                'trx' => $data
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
        } catch (Exception $e) {
            $errorDetails = "[Print Statement] error " . $e->getMessage() . " at line: " . $e->getLine();
            Log::error($errorDetails);
            abort(400, 'Request could not process.Please check log');
        }
    }
}
