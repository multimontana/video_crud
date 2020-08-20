<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;

class VideosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $videos = Video::latest()->paginate(20);
        return view('videos.index', compact('videos'))->with('1', (request()->input('page', 1) - 1) * 20);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search()
    {
        $start = array_key_exists('name_starting', $_REQUEST) ? trim($_REQUEST['name_starting']) : ' ';
        $end = array_key_exists('name_ending', $_REQUEST) ? trim($_REQUEST['name_ending']) : ' ';

        $videos = Video::where('name', 'LIKE', $start . '%')
            ->orWhere('name', 'LIKE', '%' . $end)
            ->paginate(20);

        return view('videos.index', compact('videos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('videos.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:50',
            'description' => 'required',
            'tag' => array(
                'required',
                'regex:/(^[a-zA-Z0-9,-.!? ]*$)/u'
            ),
            'url' => 'required'
        ]);

        $request['url'] = preg_replace("/\s*[a-zA-Z\/\/:\.]*youtube.com\/watch\?v=([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i", 'https://www.youtube.com/embed/$1', $request['url']);

        if (Video::create($request->all())) {
            return redirect()->route('videos.index')->with('success', "Video created");
        } else {
            return redirect()->route('videos.create')->with('Error', "Something wrong");
        }

    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Video $video
     * @return \Illuminate\Http\Response
     */
    public function show(Video $video)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Video $video
     * @return \Illuminate\Http\Response
     */
    public function edit(Video $video)
    {
        return view('videos.edit', compact('video'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Video $video
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Video $video)
    {
        $request->validate([
            'name' => 'required|max:50',
            'description' => 'required',
            'tag' => array(
                'required',
                'regex:/(^[a-zA-Z0-9,-.!? ]*$)/u'
            ),
            'url' => 'required'
        ]);

        $request['url'] = preg_replace("/\s*[a-zA-Z\/\/:\.]*youtube.com\/watch\?v=([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i", 'https://www.youtube.com/embed/$1', $request['url']);

        if ($video->update($request->all())) {
            return redirect()->route('videos.index')->with('success', "Video updated");
        } else {
            return redirect()->route('videos.create')->with('Error', "Something wrong");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Video $video
     * @return \Illuminate\Http\Response
     */
    public function destroy(Video $video)
    {
        $video->delete();
        return redirect()->route('videos.index')->with('success', "Video deleted");
    }
}
