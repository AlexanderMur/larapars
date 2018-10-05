<?php
/**
 * @var \App\Models\Donor $donor
 * @var \App\Models\Company $company
 */
?>
<div class="panel panel-default">
    <div class="panel-body">
        <h2>{{$donor->title}}</h2>

        <div>
            {{$donor->text}}
        </div>
        <div>
            <a href="{{$donor->link}}" target="_blank">
                {{$donor->link}} <i class="fa fa-external-link fa-fw"></i>
            </a>
        </div>
        <div>
            <a href="{{$donor->pivot->site}}" target="_blank">
                {{$donor->pivot->site}} <i class="fa fa-external-link fa-fw"></i>
            </a>
        </div>
        <div>
            <a href="{{route('donors.edit',$donor)}}"><i class="fa fa-edit"></i> Edit</a>
        </div>
    </div>
</div>