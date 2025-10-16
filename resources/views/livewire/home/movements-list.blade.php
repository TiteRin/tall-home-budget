<section class="card bg-base-100 shadow-xl w-full">
    <div class="card-body">
        <h2 class="card-title">
            Mouvements
        </h2>
        <ul class="list">
            @foreach($movements as $movement)
                <li>{{ $movement->memberFrom->full_name }}</li>
            @endforeach
        </ul>
    </div>
</section>
