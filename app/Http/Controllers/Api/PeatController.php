<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PageAccessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PeatController extends Controller
{
    protected $endpoint;

    public function __construct()
    {
        $this->endpoint = config('services.peat.endpoint');
    }
    public function image_analysis(Request $request)
    {
        $this->endpoint =  $this->endpoint."image_analysis";

       /* $request->validate([
            'plant_image' => 'required|string',
        ]);*/
        $request->validate([
            'plant_image' => 'required|file',
        ]);

        $file =  $request->file('plant_image');

        /*dd($file);*/


        $filename = sha1(time());

        $file->move(public_path('images/samples/'), $filename);
        $filelocation =  public_path('images/samples/' .$filename);

        Log::debug($request);

        try {
            $response = Http::withHeaders(['Api-Key' => config('services.peat.api_key')])
                ->attach('picture', fopen($filelocation, 'r'))
                ->post($this->endpoint);

            $response = json_decode($response->getBody()->getContents());

            Log::debug((array)$response);


            PageAccessLog::create(["page_link" => "peat_service/image_analysis", "page_slug" => '']);


            if (isset($response->message)) {
                return ['success' => 0, 'error' => $response->message];
            } else {
                $response->file_location = env('APP_URL') . "/images/samples/" . $filename;
                return response()->json($response);
            }
        } catch(\Exception $ex){
            return ['success' => 0, 'error' => $ex->getMessage()];
        }

    }
}
