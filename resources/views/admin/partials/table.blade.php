<?php
/**
 * @var $table
 */
?>

@if(!empty($table))
    <?php \Barryvdh\Debugbar\Facade::log($table) ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                @foreach ((array) $table[0] as $key => $value)
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
                        <td>
                            @if (is_array($value))
                                @include('admin.partials.table',['table'=>$value])
                            @else
                                {{str_limit($value,100)}}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
@endif