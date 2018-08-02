@foreach($basic_data as $one)
@if($one['type']=='dropdown')
<div class="form-group">
    <div class="col-md-12">
        <label class="col-sm-3 col-form-label">{{$one['label']}}</label>
        <div class="col-sm-9">
            <div class="row">
                <select class="frm-field sect form-control" name="{{$one['name']}}">
                    <option value="">{{_lang('app.choose')}}</option>
                    @foreach($one['values'] as $value)
                    <option {{isset($ad)&&isset($ad->$one['name'])&&$ad->$one['name']==$value->id?'selected':''}} value="{{$value->id}}">{{$value->title}}</option>	
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>
@endif
@if($one['type']=='radio')
<div class="form-group">
    <div class="col-md-12">
        <label class="col-sm-3 col-form-label">{{$one['label']}}</label>
        <div class="col-sm-9">
            <div class="product-radio">	
                @foreach($one['values'] as $value)
                <div class="check_box"> 
                    <div class="radio">
                        <label>
                            <input {{isset($ad)&&isset($ad->$one['name'])&&$ad->$one['name']==$value->id?'checked':''}} name="{{$one['name']}}" value="{{$value->id}}" type="radio"><i></i>{{$value->title}}
                        </label> 
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>
</div>
@endif
@if($one['type']=='range')
<div class="form-group">
    <div class="col-md-12">
        <label class="col-sm-3 col-form-label">{{$one['label']}}</label>
        <div class="col-sm-9">
            <div class="row">
                <select class="frm-field sect form-control" name="{{$one['name']}}">
                    <option value="">{{_lang('app.choose')}}</option>
                    @foreach(range($one['from'],$one['to']) as $value)
                    <option {{isset($ad)&&isset($ad->$one['name'])&&$ad->$one['name']==$value?'selected':''}} value="{{$value}}">{{$value}}</option>	
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>
@endif
@if($one['type']=='text')
<div class="form-group">
    <div class="col-md-12">
        <label class="col-sm-3 col-form-label">{{$one['label']}}</label>
        <div class="col-sm-9">
            <div class="row">
                <input type="text" class="form-control" name="{{$one['name']}}" value="{{isset($ad)&&isset($ad->$one['name'])?$ad->$one['name']:''}}">
            </div>
        </div>
    </div>
</div>
@endif
@endforeach

<!--<div class="form-group">
    <div class="col-md-12">
        <label class="col-sm-3 col-form-label">المساحة ( م2)</label>
        <div class="col-sm-9">
            <div class="row">
                <input type="text" class="form-control">
            </div>
        </div>
    </div>
</div>-->

