<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\Room;
use App\Models\RoomTranslation;
use Validator;
use DB;

class RoomsController extends BackendController {

    private $rules = array(
        'active' => 'required',
        'this_order' => 'required|unique:rooms'
    );

    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:rooms,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:rooms,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:rooms,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:rooms,delete', ['only' => ['delete']]);
    }

    public function index(Request $request) {
        return $this->_view('rooms/index', 'backend');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        return $this->_view('rooms/create', 'backend');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $this->rules = array_merge($this->rules,  $this->lang_rules(['title' => 'required|unique:rooms_translations,title']));
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        DB::beginTransaction();
        try {
            $room = new Room;
            $room->active = $request->input('active');
            $room->this_order = $request->input('this_order');

            $room->save();

            $room_translations = array();
            $room_title = $request->input('title');

            foreach ($this->languages as $key => $value) {
                $room_translations[] = array(
                    'locale' => $key,
                    'title' => $room_title[$key],
                    'room_id' => $room->id
                );
            }
            RoomTranslation::insert($room_translations);
            DB::commit();
            return _json('success', _lang('app.added_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            return _json('error', _lang('app.error_is_occured'), 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $find = Room::find($id);

        if ($find) {
            return _json('success', $find);
        } else {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $room = Room::find($id);

        if (!$room) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }

        $this->data['translations'] = RoomTranslation::where('room_id', $id)->get()->keyBy('locale');
        $this->data['room'] = $room;

        return $this->_view('rooms/edit', 'backend');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $room = Room::find($id);
        if (!$room) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        $this->rules['this_order'] = 'required|unique:rooms,this_order,' . $id;
        $this->rules = array_merge($this->rules, $this->lang_rules(['title' =>'required|unique:rooms_translations,title,' . $id . ',room_id']));

        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }

        DB::beginTransaction();
        try {

            $room->active = $request->input('active');
            $room->this_order = $request->input('this_order');

            $room->save();

            $room_translations = array();

            RoomTranslation::where('room_id', $room->id)->delete();

            $room_title = $request->input('title');

            foreach ($this->languages as $key => $value) {
                $room_translations[] = array(
                    'locale' => $key,
                    'title' => $room_title[$key],
                    'room_id' => $room->id
                );
            }
            RoomTranslation::insert($room_translations);

            DB::commit();
            return _json('success', _lang('app.updated_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            return _json('error', _lang('app.error_is_occured'), 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $room = Room::find($id);
        if (!$room) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        DB::beginTransaction();
        try {
            $room->delete();
            DB::commit();
            return _json('success', _lang('app.deleted_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            if ($ex->getCode() == 23000) {
                return _json('error', _lang('app.this_record_can_not_be_deleted_for_linking_to_other_records'), 400);
            } else {
                return _json('error', _lang('app.error_is_occured'), 400);
            }
        }
    }

    public function data(Request $request) {

        $rooms = Room::Join('rooms_translations', 'rooms.id', '=', 'rooms_translations.room_id')
                ->where('rooms_translations.locale', $this->lang_code)
                ->select([
            'rooms.id', "rooms_translations.title", "rooms.this_order", 'rooms.active',
        ]);

        return \Datatables::eloquent($rooms)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('rooms', 'edit') || \Permissions::check('rooms', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> ' . _lang('app.options');
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';
                                if (\Permissions::check('rooms', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a href="' . route('rooms.edit', $item->id) . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check('rooms', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "Rooms.delete(this);return false;" data-id = "' . $item->id . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.delete');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                $back .= '</ul>';
                                $back .= ' </div>';
                            }
                            return $back;
                        })
                        ->editColumn('active', function ($item) {
                            if ($item->active == 1) {
                                $message = _lang('app.active');
                                $class = 'label-success';
                            } else {
                                $message = _lang('app.not_active');
                                $class = 'label-danger';
                            }
                            $back = '<span class="label label-sm ' . $class . '">' . $message . '</span>';
                            return $back;
                        })
                        ->escapeColumns([])
                        ->make(true);
    }

}
