<li class="list-row">
    <div class="list-col-grow flex gap-[0.5rem]">
        @if($this->to->isJoint())
            <span class="badge badge-primary">{{ $this->from->full_name }}</span> doit mettre
            <span class="badge badge-secondary">{{ $this->amount }}</span> sur le compte joint.
        @else
            <span class="badge badge-primary">{{ $this->from->full_name }}</span> doit
            <span class="badge badge-secondary">{{ $this->amount }}</span> à
            <span class="badge badge-primary">{{ $this->to->full_name }}</span>.
        @endif
    </div>
</li>
