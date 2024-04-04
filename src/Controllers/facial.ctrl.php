<?php
require_once 'enum/FacialValidatorClientsEnum.php';

class FacialValidationLegacyController
{
    public function validatePhoto()
    {
        $reqData = file_get_contents("php://input");
        $arrayData = json_decode($reqData);

        $reqData = [
            'img1_path' => "$arrayData[0]",
            'img2_path' => "$arrayData[1]",
            'model_name' => "Facenet",
            'detector_backend' => "mtcnn",
            'distance_metric' => "cosine",
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => FacialValidatorClientsEnum::DEEPFACE_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>json_encode($reqData),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        if ($response === false) {
            $error = curl_error($curl);
            echo 'Erro cURL: ' . $error;
        }

        $this->response('success', $response);
    }

    private function response(string $status, string $data, $code = 200, string $message = null)
    {
        http_response_code($code);
        header('Content-Type: application/json');
        die(json_encode(compact('status', 'message', 'data', 'code')));
    }
}