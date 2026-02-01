<ul>
    @foreach ($expenseTabs as $tab)
        <li>{{ $tab->name }}</li>
    @endforeach
</ul>
