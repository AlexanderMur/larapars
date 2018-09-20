<?php
/**
 * @var $table
 */
?>

@if(!empty($table))

    <?php
    if(!is_array(reset($table))){
        $table = [$table];
    }
    ?>
    <?php \Barryvdh\Debugbar\Facade::log($table) ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                @foreach ((array) reset($table) as $key => $value)
                    <td>
                        {{$key}}
                    </td>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($table as $row)
                <tr>
                    @foreach ( (array) $row as $key => $value)
                        <td style="height:40px; overflow:hidden">
                            @if (is_array($value))
                                @include('admin.partials.table',['table'=>$value])
                            @else
                                {{$value}}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
@endif