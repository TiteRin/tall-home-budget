<div class="tabs tabs-lift tabs-xl" role="tablist">
    <label class="tab">
        <input type="radio" name="tabs-expense-tabs" checked/>
        Nouvel onglet de dépense
    </label>
    <div class="tab-content bg-base-100 border-base-300 p-6">
        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aut debitis eos et iste odit, officiis reiciendis?
        Animi architecto ex fugit labore magnam optio quibusdam sequi! Atque hic provident quam soluta.
    </div>

    @foreach ($expenseTabs as $tab)
        <label class="tab">
            <input type="radio" name="tabs-expense-tabs"/>
            {{ $tab->name }}
        </label>
        <div class="tab-content bg-base-100 border-base-300 p-6">

        </div>
    @endforeach
</div>
