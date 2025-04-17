<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SampleController extends Controller
{
    /**
     * Return a sample CSV file
     *
     * @return \Illuminate\Http\Response
     */
    public function sampleCsv()
    {
        $csv = "corporate_reference_number,guarantee_type,nominal_amount,nominal_amount_currency,expiry_date,applicant_name,applicant_address,beneficiary_name,beneficiary_address\n";
        $csv .= "TFG-20250501-ABC123,Bank,50000.00,USD,2025-12-31,\"Global Trading Corp\",\"123 Commerce St, New York, NY 10001, USA\",\"International Suppliers Ltd\",\"456 Export Ave, London, EC2R 8AH, UK\"\n";
        $csv .= "TFG-20250502-DEF456,\"Bid Bond\",25000.00,EUR,2025-09-15,\"European Construction Group\",\"78 Boulevard Saint-Michel, 75006 Paris, France\",\"City of Berlin\",\"Rathausstraße 15, 10178 Berlin, Germany\"\n";
        $csv .= "TFG-20250503-GHI789,Insurance,100000.00,CAD,2026-03-31,\"North American Shipping Inc\",\"500 Lakeshore Blvd, Toronto, ON M5V 2V9, Canada\",\"Pacific Marine Insurers\",\"888 Harbor Dr, Vancouver, BC V6C 3E8, Canada\"\n";
        $csv .= "TFG-20250504-JKL012,Surety,75000.00,GBP,2025-11-30,\"UK Construction Partners\",\"45 Oxford Street, London, W1D 2DZ, UK\",\"Scotland Development Authority\",\"100 Royal Mile, Edinburgh, EH1 1SG, Scotland\"\n";
        $csv .= "TFG-20250505-MNO345,Bank,200000.00,USD,2026-02-28,\"American Export Company\",\"1200 Market St, Philadelphia, PA 19107, USA\",\"Asian Import Consortium\",\"88 Connaught Road, Central, Hong Kong\"";

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="sample_guarantees.csv"');
    }

    /**
     * Return a sample JSON file
     *
     * @return \Illuminate\Http\Response
     */
    public function sampleJson()
    {
        $json = [
            [
                "corporate_reference_number" => "TFG-20250501-ABC123",
                "guarantee_type" => "Bank",
                "nominal_amount" => 50000.00,
                "nominal_amount_currency" => "USD",
                "expiry_date" => "2025-12-31",
                "applicant_name" => "Global Trading Corp",
                "applicant_address" => "123 Commerce St, New York, NY 10001, USA",
                "beneficiary_name" => "International Suppliers Ltd",
                "beneficiary_address" => "456 Export Ave, London, EC2R 8AH, UK"
            ],
            [
                "corporate_reference_number" => "TFG-20250502-DEF456",
                "guarantee_type" => "Bid Bond",
                "nominal_amount" => 25000.00,
                "nominal_amount_currency" => "EUR",
                "expiry_date" => "2025-09-15",
                "applicant_name" => "European Construction Group",
                "applicant_address" => "78 Boulevard Saint-Michel, 75006 Paris, France",
                "beneficiary_name" => "City of Berlin",
                "beneficiary_address" => "Rathausstraße 15, 10178 Berlin, Germany"
            ],
            [
                "corporate_reference_number" => "TFG-20250503-GHI789",
                "guarantee_type" => "Insurance",
                "nominal_amount" => 100000.00,
                "nominal_amount_currency" => "CAD",
                "expiry_date" => "2026-03-31",
                "applicant_name" => "North American Shipping Inc",
                "applicant_address" => "500 Lakeshore Blvd, Toronto, ON M5V 2V9, Canada",
                "beneficiary_name" => "Pacific Marine Insurers",
                "beneficiary_address" => "888 Harbor Dr, Vancouver, BC V6C 3E8, Canada"
            ],
            [
                "corporate_reference_number" => "TFG-20250504-JKL012",
                "guarantee_type" => "Surety",
                "nominal_amount" => 75000.00,
                "nominal_amount_currency" => "GBP",
                "expiry_date" => "2025-11-30",
                "applicant_name" => "UK Construction Partners",
                "applicant_address" => "45 Oxford Street, London, W1D 2DZ, UK",
                "beneficiary_name" => "Scotland Development Authority",
                "beneficiary_address" => "100 Royal Mile, Edinburgh, EH1 1SG, Scotland"
            ],
            [
                "corporate_reference_number" => "TFG-20250505-MNO345",
                "guarantee_type" => "Bank",
                "nominal_amount" => 200000.00,
                "nominal_amount_currency" => "USD",
                "expiry_date" => "2026-02-28",
                "applicant_name" => "American Export Company",
                "applicant_address" => "1200 Market St, Philadelphia, PA 19107, USA",
                "beneficiary_name" => "Asian Import Consortium",
                "beneficiary_address" => "88 Connaught Road, Central, Hong Kong"
            ]
        ];

        return response(json_encode($json, JSON_PRETTY_PRINT))
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="sample_guarantees.json"');
    }

    /**
     * Return a sample XML file
     *
     * @return \Illuminate\Http\Response
     */
    public function sampleXml()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
<guarantees>
  <guarantee>
    <corporate_reference_number>TFG-20250501-ABC123</corporate_reference_number>
    <guarantee_type>Bank</guarantee_type>
    <nominal_amount>50000.00</nominal_amount>
    <nominal_amount_currency>USD</nominal_amount_currency>
    <expiry_date>2025-12-31</expiry_date>
    <applicant_name>Global Trading Corp</applicant_name>
    <applicant_address>123 Commerce St, New York, NY 10001, USA</applicant_address>
    <beneficiary_name>International Suppliers Ltd</beneficiary_name>
    <beneficiary_address>456 Export Ave, London, EC2R 8AH, UK</beneficiary_address>
  </guarantee>
  <guarantee>
    <corporate_reference_number>TFG-20250502-DEF456</corporate_reference_number>
    <guarantee_type>Bid Bond</guarantee_type>
    <nominal_amount>25000.00</nominal_amount>
    <nominal_amount_currency>EUR</nominal_amount_currency>
    <expiry_date>2025-09-15</expiry_date>
    <applicant_name>European Construction Group</applicant_name>
    <applicant_address>78 Boulevard Saint-Michel, 75006 Paris, France</applicant_address>
    <beneficiary_name>City of Berlin</beneficiary_name>
    <beneficiary_address>Rathausstraße 15, 10178 Berlin, Germany</beneficiary_address>
  </guarantee>
  <guarantee>
    <corporate_reference_number>TFG-20250503-GHI789</corporate_reference_number>
    <guarantee_type>Insurance</guarantee_type>
    <nominal_amount>100000.00</nominal_amount>
    <nominal_amount_currency>CAD</nominal_amount_currency>
    <expiry_date>2026-03-31</expiry_date>
    <applicant_name>North American Shipping Inc</applicant_name>
    <applicant_address>500 Lakeshore Blvd, Toronto, ON M5V 2V9, Canada</applicant_address>
    <beneficiary_name>Pacific Marine Insurers</beneficiary_name>
    <beneficiary_address>888 Harbor Dr, Vancouver, BC V6C 3E8, Canada</beneficiary_address>
  </guarantee>
  <guarantee>
    <corporate_reference_number>TFG-20250504-JKL012</corporate_reference_number>
    <guarantee_type>Surety</guarantee_type>
    <nominal_amount>75000.00</nominal_amount>
    <nominal_amount_currency>GBP</nominal_amount_currency>
    <expiry_date>2025-11-30</expiry_date>
    <applicant_name>UK Construction Partners</applicant_name>
    <applicant_address>45 Oxford Street, London, W1D 2DZ, UK</applicant_address>
    <beneficiary_name>Scotland Development Authority</beneficiary_name>
    <beneficiary_address>100 Royal Mile, Edinburgh, EH1 1SG, Scotland</beneficiary_address>
  </guarantee>
  <guarantee>
    <corporate_reference_number>TFG-20250505-MNO345</corporate_reference_number>
    <guarantee_type>Bank</guarantee_type>
    <nominal_amount>200000.00</nominal_amount>
    <nominal_amount_currency>USD</nominal_amount_currency>
    <expiry_date>2026-02-28</expiry_date>
    <applicant_name>American Export Company</applicant_name>
    <applicant_address>1200 Market St, Philadelphia, PA 19107, USA</applicant_address>
    <beneficiary_name>Asian Import Consortium</beneficiary_name>
    <beneficiary_address>88 Connaught Road, Central, Hong Kong</beneficiary_address>
  </guarantee>
</guarantees>';

        return response($xml)
            ->header('Content-Type', 'application/xml')
            ->header('Content-Disposition', 'attachment; filename="sample_guarantees.xml"');
    }
}