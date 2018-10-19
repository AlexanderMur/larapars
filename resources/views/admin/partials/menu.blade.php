<ul class="nav" id="side-menu">
    <li>
        <a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
    </li>
    <li>
        <a href="{{route('companies.index')}}"><i class="fa fa-car  fa-fw"></i> Компании</a>
    </li>
    <li>
        <a href="{{route('parsed_companies.index')}}"><i class="fa fa-car  fa-fw"></i> Модерация</a>
    </li>
    <li>
        <a href="{{route('reviews.new')}}"><i class="fa fa-comments fa-fw"></i> Отзывы</a>
    </li>
    <li>
        <a href="{{route('pars.manual')}}"><i class="fa fa-car fa-fw"></i> Тест вручную</a>
    </li>
    <li>
        <a href="{{route('logs.index')}}"><i class="fa fa-car fa-fw"></i> Логи</a>
    </li>
    <li>
        <a href="{{route('donors.index')}}"><i class="fa fa-thumb-tack fa-fw"></i> Доноры</a>
    </li>
    <li>
        <a href="{{route('donors.create')}}"><i class="fa fa-thumb-tack fa-fw"></i> Создать донора</a>
    </li>
    <li>
        <a href="{{route('admin.export')}}" target="_blank"><i class="fa fa-download fa-fw"></i> Експорт</a>
    </li>
    <li>
        <a href="{{route('admin.settings')}}"><i class="fa fa-gear fa-fw"></i> Настройки</a>
    </li>
</ul>