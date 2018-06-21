<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\BackendController;
use App\Models\AdReport;

class AdReportsController extends BackendController
{
    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:ad_reports,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:ad_reports,add', ['only' => ['store']]);
    }
    public function index() {
        return $this->_view('reports/index', 'backend');
    }
        /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $find = AdReport::find($id);
        if ($find) {
            return response()->json([
                        'type' => 'success',
                        'message' => AdReport::transform($find)
            ]);
        } else {
            return response()->json([
                        'type' => 'success',
                        'message' => 'error'
            ]);
        }
    }

    public function destroy(Request $request) {
        $ids = $request->input('ids');
        try {
            AdReport::destroy($ids);
            return _json('success', _lang('app.deleted_successfully'));
        } catch (Exception $ex) {
            return _json('error', _lang('app.error_is_occured'));
        }
    }
    public function data() {
        $reprot = AdReport::select('*');
        // $reprot=AdReport::transformCollection($reprot);
        
        return \Datatables::eloquent($reprot)
        
                ->addColumn('input', function ($item) {
                    // dd($item);
                    $back = '';

                    $back = '<div class="md-checkbox col-md-4" style="margin-left:40%;">';
                    $back .= '<input type="checkbox" id="' . $item->id . '" data-id="' . $item->id . '" class="md-check check-one-message"  value="">';
                    $back .= '<label for="' . $item->id . '">';
                    $back .= '<span></span>';
                    $back .= '<span class="check"></span>';
                    $back .= '<span class="box"></span>';
                    $back .= '</label>';
                    $back .= '</div>';

                    return $back;
                })
                ->addColumn('user', function ($item) {

                    $back = "";

                    $back .= '<a href="'.route('users.show',$item->user->id).'"  data-id = "' . $item->user->id . '">';
                    $back .= $item->user->username;
                    $back .= '</a>';
                    return $back;
                })
                ->addColumn('ad', function ($item) {

                    $back = "";

                    $back .= '<a href="'.route('ads.show',$item->ad->id).'"  data-id = "' . $item->ad->id . '">';
                    $back .= $item->ad->title;
                    $back .= '</a>';
                    return $back;
                })
                ->addColumn('report', function ($item) {

                    $back = "";
                    $back .= _lang('app.'.AdReport::$types[$item->type]);
                    return $back;
                })
                
                ->escapeColumns([])
                ->make(true);
    }
}
