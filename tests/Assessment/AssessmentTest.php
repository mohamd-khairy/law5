<?php

namespace App\Tests\Assessment;

use App\Tests\TestCase;

class AssessmentTest extends TestCase
{
    /**
     * AssessmentTest constructor.
     * @param string|null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->setDataDirectory(realpath(__DIR__ . '/data/'));
    }

    /**
     * @dataProvider detailedAssessmentDataProvider
     * @param $inputData
     * @param $expectedOutput
     * @param int $expectedStatusCode
     */
    public function testDetailedAssessmentApi($inputData, $expectedOutput, $expectedStatusCode = 200)
    {
        $this->json(
            'post',
            '/api/detailed_assessment',
            json_decode($inputData, true)
        );

        $outputData = $this->response->getContent();
        $this->assertEquals($expectedStatusCode, $this->response->getStatusCode());
        $this->assertEquals(json_decode($expectedOutput), json_decode($outputData));
    }

    /**
     *
     */
    public function testTotalsAssessmentApi()
    {
        $inputData = $this->getJsonFromFile('totalsInput-1.json');
        $expectedOutput = $this->getJsonFromFile('totalsOutput-1.json');

        $inputJson = json_decode($inputData, true);

        $this->json(
            'post',
            '/api/totals_assessment',
            $inputJson
        );

        $outputData = $this->response->getContent();
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals(json_decode($expectedOutput), json_decode($outputData));
    }

    /**
     *
     */
    public function detailedAssessmentDataProvider()
    {
        $data = [
            ['detailedInput-1.json', 'detailedOutput-1.json', 200],
            ['detailedInput-2.json', 'detailedOutput-2.json', 200],
            ['detailedInput-3-originalExcelExample.json', 'detailedOutput-3-originalExcelExample.json', 200],
            ['detailedInput-4.json', 'detailedOutput-4.json', 200],
            ['detailedInput-5.json', 'detailedOutput-5.json', 422],
        ];

        foreach ($data as $set) {
            $inputData = $this->getJsonFromFile($set[0]);
            $expectedOutput = $this->getJsonFromFile($set[1]);
            yield [$inputData, $expectedOutput, $set[2]];
        }
    }
}
