// app/Http/Controllers/BantuanController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BantuanController extends Controller
{
    public function getDataForPopup()
    {
        // Data yang akan dikirim ke frontend (Vue)
        $data = [
            'exampleField' => 'Ini adalah data yang diambil dari server.',
        ];

        return response()->json($data);  // Mengirim data dalam format JSON
    }
}
