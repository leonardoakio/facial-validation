<?php

namespace App\Http\Controllers\FacialValidator;

use Illuminate\Http\Request;
use App\Repositories\FacialValidator\FacialValidatorRepositoryInterface;

class FacialValidationController
{
    public function __construct(
        protected Request $request,
        protected FacialValidatorRepositoryInterface $facialValidatorRepository,
    ) {}

    public function validatePhoto()
    {
        if (!$this->request->has('img1_path') || !$this->request->has('img2_path')) {
            return response()->json(['error' => "Campos 'img1_path' e 'img1_path' obrigatórios"], 400);
        }

        $data = $this->request->input();

        $reqData = [
            'img1_path' => $data['img1_path'],
            'img2_path' => $data['img2_path'],
            'model_name' => "Facenet",
            'detector_backend' => "mtcnn",
            'distance_metric' => "cosine",
        ];

        $response = $this->facialValidatorRepository->verifyPhoto(json_encode($reqData));

        return response()->json([
            'data' => $response
        ]);
    }
}