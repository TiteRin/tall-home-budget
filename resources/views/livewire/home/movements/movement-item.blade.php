<li class="list-row">
    <div class="list-col-grow flex gap-[0.5rem]">
        @if($this->to->isJoint())
            {{ $this->from->full_name }} doit mettre
            {{ $this->amount }} sur le compte joint.
        @else
            {{ $this->from->full_name }} doit
            {{ $this->amount }} à
            {{ $this->to->full_name }}.
        @endif
    </div>
</li>
