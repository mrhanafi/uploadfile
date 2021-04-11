<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class FileUpload extends Controller
{
    public function createForm()
    {
        // $path = Storage::disk('public')->files('uploads');
        // $path = storage_path() . "\\uploads";
        // $dir = Storage::disk('public')->files();
        // dd($dir);

        $files = File::all();
        return view('file-upload', compact('files'));
    }

    public function fileUpload(Request $req)
    {
        $req->validate([
            'file' => 'required|mimes:csv,txt,xlx,xls,pdf,png,jpg,jpeg|max:2048',
        ]);

        $fileModel = new File();

        if ($req->file()) {
            $fileName = time() . '_' . $req->file->getClientOriginalName();
            $filePath = $req->file('file')->storeAs('uploads', $fileName, 'public');

            $fileModel->name = time() . '_' . $req->file->getClientOriginalName();
            $fileModel->file_path = '/storage/' . $filePath;
            $fileModel->size = $req->file->getSize();
            $fileModel->uploaded_by = Auth::id();
            // dd($fileModel);
            $fileModel->save();

            return back()
                ->with('success', 'File has been uploaded.')
                ->with('file', $fileName);
        }
    }

    public function getFile($filename)
    {
        $path = storage_path() . "\\app\\public\\uploads\\" . $filename;
        // dd($path);
        if (file_exists($path)) {
            return Response::download($path);
        }

        // return (new Response($file, 200))
        //     ->header('Content-Type', 'image/jpeg');
    }
}
