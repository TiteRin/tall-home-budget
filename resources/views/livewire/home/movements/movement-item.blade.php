<li class="list-row">
    @if($this->to->isJoint())
        <em>{{ $this->from->full_name }}</em> doit mettre <em>{{ $this->amount }}</em> sur le compte joint.
    @else
        <em>{{ $this->from->full_name }}</em> doit <em>{{ $this->amount }}</em> à <em>{{ $this->to->full_name }}</em>.
    @endif
</li>
