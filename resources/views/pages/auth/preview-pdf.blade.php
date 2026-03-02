{{-- Auth: Preview PDF --}}
@extends('layouts.guest')

@section('title', 'Preview PDF - ' . __('messages.app_name'))

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-2xl">
        <x-login-card>
            {{-- Header --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary-100 mb-4">
                    <svg class="w-8 h-8 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-text-primary" data-translate="preview_pdf_title">
                    {{ __('messages.preview_pdf_title') }}
                </h1>
                <p class="mt-2 text-sm text-text-secondary" data-translate="preview_pdf_subtitle">
                    {{ __('messages.preview_pdf_subtitle') }}
                </p>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('preview.pdf.id') }}" id="previewPdfForm" class="space-y-6">
                @csrf

                {{-- Full Name --}}
                <x-input
                    name="full_name"
                    :label="__('messages.full_name')"
                    :dataTranslateLabel="'full_name'"
                    :required="true"
                    :value="old('full_name')"
                    :placeholder="__('messages.full_name_placeholder')"
                />

                {{-- Batch --}}
                <x-input
                    name="batch"
                    type="number"
                    :label="__('messages.batch')"
                    :dataTranslateLabel="'batch'"
                    :required="true"
                    :value="old('batch')"
                    min="1"
                    max="26"
                    :placeholder="__('messages.batch_placeholder')"
                />

                {{-- Citizen ID --}}
                <x-input
                    name="citizen_id"
                    :label="__('messages.citizen_id')"
                    :dataTranslateLabel="'citizen_id'"
                    :required="true"
                    :value="old('citizen_id')"
                    :placeholder="__('messages.citizen_id_placeholder')"
                />

                {{-- Signature --}}
                <x-signature-input
                    name="signature"
                    :label="__('messages.signature')"
                    :dataTranslateLabel="'signature'"
                    :required="true"
                    :dataTranslateHint="'signature_hint'"
                />

                {{-- Submit Button --}}
                <div class="pt-4">
                    <button
                        type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-500 hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors"
                        data-translate="preview_pdf_generate"
                    >
                        {{ __('messages.preview_pdf_generate') }}
                    </button>
                </div>

                {{-- Back to Login Link --}}
                <div class="text-center mt-6">
                    <a href="{{ route('login') }}" class="text-sm text-text-secondary hover:text-primary-500 transition-colors">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span data-translate="back_to_login">{{ __('messages.back_to_login') }}</span>
                    </a>
                </div>
            </form>

            @if($errors->any())
                <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="text-sm text-red-600">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </x-login-card>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('previewPdfForm');

    form.addEventListener('submit', function(e) {
        const signatureData = document.getElementById('signature_data').value;

        if (!signatureData || signatureData.trim() === '') {
            e.preventDefault();
            alert('{{ __("messages.signature_required") }}');
            return false;
        }
    });
});
</script>
@endpush
