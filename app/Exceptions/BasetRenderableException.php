<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class BasetRenderableException extends \Exception
{
    public function render(Request $request): Response|null
    {
        if ($request->expectsJson() || $request->is("api/*")) // may wish to use instead:  if ($request->is('api/*'))
        {
            $errorArrayBase = [
                "code"    => $this->getCode(),
                "message" => $this->getMessage()
            ];

            $errorArray = array_merge($errorArrayBase, $this->getAdditionalJsonErrorItems());

            // API JSON response
            $response = \response()->json(['error' => $errorArray]);

            $response->setStatusCode(400);
        } else {
            // web view response
            $response = null;
        }

        return $response;
    }


    /**
     * Get the array of any extra items that should form part of the error array that gets returned.
     * Most of the time you just want to return [];
     */
    protected function getAdditionalJsonErrorItems(): array
    {
        return [];
    }
}
