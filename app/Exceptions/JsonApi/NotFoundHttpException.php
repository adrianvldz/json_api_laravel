<?php

namespace App\Exceptions\JsonApi;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class NotFoundHttpException extends Exception
{
    public function render(Request $request)
    {
      
        return response()->json([
            'errors' => [
                [
                    'title' => 'Not Found',
                    'detail' => $this->getDetail($request),
                    'status' => '404',
                ],
            ],
        ], 404);
    }

    protected function getDetail($request): string
    {
        if(str($this->getMessage())->startsWith('No query results for model')){
            return "No records found with the id '{$request->getResourceId()}' 
            in the '{$request->getResourcceType()}' resource.";
            
        }

        return $this->getMessage();
    }
}
