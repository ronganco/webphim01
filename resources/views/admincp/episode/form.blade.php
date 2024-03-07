@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
              <a href="{{route('episode.index')}}" class="btn btn-primary">Liệt Kê Danh Sách Tập Phim</a>
                <div class="card-header">Quản Lý Tập Phim</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if(!isset($episode))
                        {!! Form::open(['route'=>'episode.store','method'=>'POST','enctype'=>'multipart/form-data']) !!}
                    @else
                        {!! Form::open(['route'=>['episode.update',$episode->id],'method'=>'PUT','enctype'=>'multipart/form-data']) !!}
                    @endif
                        
                        <div class="form-group">
                            {!! Form::label('movie', 'Chọn Phim', []) !!}
                            {!! Form::select('movie_id', ['0'=> 'Chọn phim', 'Phim mới nhất'=>$list_movie], isset($episode) ? $episode->movie_id : '', ['class'=>'form-control select-movie']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('link', 'Link phim', []) !!}
                            {!! Form::text('link', isset($episode) ? $episode->linkphim : '', ['class'=>'form-control','placeholder'=>'...',]) !!}
                        </div>
                        @if(isset($episode))
                        <div class="form-group">
                            {!! Form::label('episode', 'Tập phim', []) !!}
                            {!! Form::text('episode', isset($episode) ? $episode->episode : '', ['class'=>'select-episode form-control','placeholder'=>'...', isset($episode) ? 'readonly' : '']) !!}
                        </div>
                        @else
                        <div class="form-group">
                            {!! Form::label('episode', 'Tập phim', []) !!}
                            <select name="episode" class="form-control select-episode" id="show_movie"></select>
                        </div>
                        @endif
                        @if(isset($episode))
                        <div class="form-group">
                            {!! Form::label('episode', 'VIP', []) !!}
                            <select name="vip" class="form-control" id="vip">
                                <option value="0" @if($episode->vip == 0) selected @endif>
                                    Thường
                                </option>
                                <option value="1" @if($episode->vip == 1) selected @endif>
                                    Vip
                                </option>
                            </select>
                        </div>
                        @else
                        <div class="form-group">
                            {!! Form::label('episode', 'VIP', []) !!}
                            <select name="vip" class="form-control" id="vip">
                                <option value="0">
                                    Thường
                                </option>
                                <option value="1">
                                    Vip
                                </option>
                            </select>                        </div>
                        @endif
                        
                        @if(!isset($episode))
                            {!! Form::submit('Thêm Tập Phim', ['class'=>'btn btn-success']) !!}
                        @else
                            {!! Form::submit('Cập Nhật Tập Phim', ['class'=>'btn btn-success']) !!}
                        @endif
                    {!! Form::close() !!}
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection
