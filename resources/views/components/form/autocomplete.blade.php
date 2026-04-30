<div {{ $attributes }}>

<label class="form-label">

{{ $label }}

</label>

<input
type="text"
id="{{ $id }}_search"
class="form-control form-control-sm"
value="{{ $text }}">

<input
type="hidden"
name="{{ $name }}"
id="{{ $id }}"
value="{{ $value }}">

</div>