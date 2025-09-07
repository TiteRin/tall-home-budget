<section class="card bg-base-100 shadow-xl w-full">
    <div class="card-body">
        <h2 class="card-title">
            Mouvements
        </h2>
        <ul class="list">
            @foreach($members as $member)
                <li class="list-row">
                    {{ $member->first_name }} doit à XXXX la somme de YYY.YY €
                </li>
            @endforeach
        </ul>
    </div>
</section>
