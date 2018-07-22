<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\Module;
use App\Models\ContactMessage;
use Validator;
use Mail;
use App\Mail\ContactMessageMail;

class ContactMessagesController extends BackendController {

    private $rules = array(
        'reply' => 'required'
    );

    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:contact_messages,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:contact_messages,add', ['only' => ['store']]);
    }

    public function index() {
        return $this->_view('contact_messages/index', 'backend');
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
        $find = ContactMessage::find($id);
        if ($find) {
            return  _json('success', $find);
        } else {
            return _json('error', _lang('app.error_is_occured'), 400);
        }
    }

    public function reply(Request $request) {

        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        } else {
            try {
                $contact_message = ContactMessage::find($request->input('contact_message_id'));
                if (!$contact_message) {
                    return _json('error', _lang('app.error_is_occured'), 400);
                }
                $contact_message->reply = $request->input('reply');
                $contact_message->save();
                Mail::to( $contact_message->email)->send(new ContactMessageMail($contact_message->message,$contact_message->reply));
                return _json('success', _lang('app.sent_successfully'));
            } catch (\Exception $ex) {
                return _json('error', _lang('app.error_is_occured'), 400);
            }
        }
    }

    public function destroy(Request $request) {
        $ids = $request->input('ids');
        try {
            ContactMessage::destroy($ids);
            return _json('success', _lang('app.deleted_successfully'));
        } catch (Exception $ex) {
            return _json('error', _lang('app.error_is_occured'));
        }
    }

    public function data() {
        $blog = ContactMessage::select(['id', 'email', 'type', 'name', 'created_at']);

        return \Datatables::eloquent($blog)
                        ->addColumn('options', function ($item) {

                            $back = "";

                            $back .= '<a href="" class="btn btn-info" onclick = "Contact_messages.viewMessage(this);return false;" data-id = "' . $item->id . '">';
                            $back .= '' . _lang('app.view') . '';
                            $back .= '</a>';
                            return $back;
                        })
                        ->addColumn('input', function ($item) {

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
                        ->editColumn('type', function ($item) {
                            $back = '';
                            if (isset(ContactMessage::$types[$item->type])) {
                                $back = _lang('app.' . ContactMessage::$types[$item->type]);
                            }
                            return $back;
                        })
                        ->escapeColumns([])
                        ->make(true);
    }

}
