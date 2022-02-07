
<?php
    
    # A2lA Email Scraper 
    error_reporting(E_ERROR | E_PARSE);
    require 'vendor/autoload.php';

    # Get JSON List of entries
    $data = file_get_contents("data.json");

    $data_decoded = json_decode($data, true);

    # Set object for extracted data 
    $extractedEmails = [];

    # Iterate through array to get labPIDs
    foreach ($data_decoded as $items) {
        $labPID = $items['labPID'];

        $httpClient = new \GuzzleHttp\Client();
        $response = $httpClient->get('https://customer.a2la.org/index.cfm?event=directory.detail&labPID=' . $labPID);

        $htmlString = (string) $response->getBody();
        
        # add this line to suppress any warnings
        libxml_use_internal_errors(true);

        $doc = new DOMDocument();
        $doc->loadHTML($htmlString);
        $xpath = new DOMXPath($doc);

        $titles = $xpath->evaluate('//p[@class="form-control-static"]//em/a');
        
        foreach ($titles as $title) {
            $extractedEmails[] = $title->textContent.PHP_EOL;
        }
    }
    
    foreach (array_unique($extractedEmails) as $emails) {
        echo $emails . "<br>";
    }