<div class="form-group">
    <div class="col-md col-form-label text-md-right py-md-0">
        {{ $field->label }}
    </div>

    <div class="col-md">
        @foreach($field->options as $value => $label)
            <div class="form-check">
                <input
                    id="{{ $field->name . '.' . $loop->index }}"
                    type="checkbox"
                    class="form-check-input {{ $field->input_class }} @error($field->key) is-invalid @enderror"
                    value="{{ $value }}"
                    wire:model.lazy="{{ $field->key }}">

                <label class="form-check-label" for="{{ $field->name . '.' . $loop->index }}">
                    {{ $label }}
                </label>
            </div>
        @endforeach

        @include('livewire-forms::fields.error-help')
    </div>
</div>
