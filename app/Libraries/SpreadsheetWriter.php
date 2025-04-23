<?php

namespace App\Libraries;

use Google\Client;
use Google\Service\Sheets;

class SpreadsheetWriter
{
    protected $spreadsheetId = '1W6LodJZ8FCgQfrSDJZ8WT3KYyFbRQ3C-EVHKaIX3ZAE';
    protected $sheetName = 'Daftar & Hasil Ujian Peserta Examnation';
    protected $client;
    protected $service;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAuthConfig(WRITEPATH . 'credentials/western-octagon-457708-m7-5be6fe76e51c.json');
        $this->client->addScope(Sheets::SPREADSHEETS);
        $this->service = new Sheets($this->client);
    }

    public function appendRow(array $rowData)
    {
        $range = "'$this->sheetName'!A:D";
        $body = new Sheets\ValueRange([
            'values' => [$rowData]
        ]);

        $params = [
            'valueInputOption' => 'USER_ENTERED'
        ];

        $this->service->spreadsheets_values->append(
            $this->spreadsheetId,
            $range,
            $body,
            $params
        );
    }
    public function getAllRows()
    {
        $range = "'{$this->sheetName}'!A:D"; 
        $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
        return $response->getValues();
    }

}
