@extends('layouts.guest')

@section('title', __('messages.register'))

@section('content')
<x-login-card :padding="'lg'" x-data="registerFormController()">
    {{-- Page Header with Logo --}}
    <x-page-header
        :title="__('messages.register')"
        :subtitle="__('messages.register_subtitle')"
        :dataTranslateTitle="'register'"
        :dataTranslateSubtitle="'register_subtitle'"
    />

    {{-- Error Messages --}}
    @if(session('error'))
        <x-alert type="danger" :icon="true">
            <p class="text-sm">{{ session('error') }}</p>
        </x-alert>
    @endif

    @if ($errors->any())
        <x-alert type="danger" :icon="true" :dismissible="true">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    {{-- Register Form --}}
    <form method="POST" action="{{ route('register.post') }}" class="space-y-6" enctype="multipart/form-data">
        @csrf

        {{-- Account Information Section --}}
        <div class="space-y-4">
            <h3 class="text-sm font-semibold text-text-primary border-b border-border pb-2" data-translate="account_info">
                {{ __('messages.account_info') }}
            </h3>

            {{-- Full Name --}}
            <x-input
                type="text"
                name="full_name"
                :label="__('messages.full_name')"
                :dataTranslateLabel="'full_name'"
                :dataTranslatePlaceholder="'full_name_placeholder'"
                placeholder="{{ __('messages.full_name_placeholder') }}"
                :required="true"
                autocomplete="name"
                autocorrect="off"
                autocapitalize="words"
            />

            {{-- 4 Digit PIN --}}
            <x-input
                type="password"
                name="pin"
                :label="__('messages.pin')"
                :dataTranslateLabel="'pin'"
                :dataTranslatePlaceholder="'pin_placeholder'"
                placeholder="{{ __('messages.pin_placeholder') }}"
                :required="true"
                maxlength="4"
                pattern="[0-9]{4}"
                inputmode="numeric"
                autocomplete="new-password"
                :hint="__('messages.pin_hint')"
                :dataTranslateHint="'pin_hint'"
            />

            {{-- Profile Photo (Optional) --}}
            <x-file-input
                name="photo_profile"
                :label="__('messages.profile_photo') . ' (' . __('messages.optional') . ')'"
                :dataTranslateLabel="'profile_photo'"
                :dataTranslateUpload="'click_to_upload'"
                accept="image/png,image/jpeg,image/jpg"
                :required="false"
                :hint="__('messages.profile_photo_hint')"
                :dataTranslateHint="'profile_photo_hint'"
            />

            {{-- Batch --}}
            <x-input
                type="number"
                name="batch"
                :label="__('messages.batch')"
                :dataTranslateLabel="'batch'"
                :dataTranslatePlaceholder="'batch_placeholder'"
                placeholder="{{ __('messages.batch_placeholder') }}"
                :required="true"
                min="1"
                max="26"
                onkeydown="return event.key !== 'e' && event.key !== '-' && event.key !== '.'"
            />

            {{-- Tanggal Join / Tanggal Masuk --}}
            <x-input
                type="date"
                name="tanggal_masuk"
                :label="__('messages.join_date')"
                :dataTranslateLabel="'join_date'"
                :required="true"
            />
        </div>

        {{-- Personal Information Section --}}
        <div class="space-y-4">
            <h3 class="text-sm font-semibold text-text-primary border-b border-border pb-2" data-translate="personal_info">
                {{ __('messages.personal_info') }}
            </h3>

            {{-- Citizen ID --}}
            <x-input
                type="text"
                name="citizen_id"
                :label="__('messages.citizen_id')"
                :dataTranslateLabel="'citizen_id'"
                :dataTranslatePlaceholder="'citizen_id_placeholder'"
                placeholder="{{ __('messages.citizen_id_placeholder') }}"
                :required="true"
            />

            {{-- Phone Number --}}
            <x-input
                type="tel"
                name="no_hp_ic"
                :label="__('messages.phone_number')"
                :dataTranslateLabel="'phone_number'"
                :dataTranslatePlaceholder="'phone_number_placeholder'"
                placeholder="{{ __('messages.phone_number_placeholder') }}"
                :required="true"
            />

            {{-- Gender --}}
            <x-select
                name="jenis_kelamin"
                :label="__('messages.gender')"
                :dataTranslateLabel="'gender'"
                :dataTranslateOptions="true"
                :dataTranslatePlaceholder="'select_gender'"
                :options="[
                    ['value' => '', 'label' => __('messages.select_gender')],
                    ['value' => 'Laki-laki', 'label' => __('messages.gender_male')],
                    ['value' => 'Perempuan', 'label' => __('messages.gender_female')]
                ]"
                :required="true"
            />
        </div>

        {{-- Documents Section --}}
        <div class="space-y-4">
            <h3 class="text-sm font-semibold text-text-primary border-b border-border pb-2" data-translate="identity_documents">
                {{ __('messages.identity_documents') }}
            </h3>

            {{-- KTP File --}}
            <x-file-input
                name="file_ktp"
                :label="__('messages.ktp_file')"
                :dataTranslateLabel="'ktp_file'"
                :dataTranslateUpload="'click_to_upload'"
                accept="image/png,image/jpeg"
                :required="true"
                :hint="__('messages.file_formats_hint')"
                :dataTranslateHint="'file_formats_hint'"
            />

            {{-- SKB File --}}
            <x-file-input
                name="file_skb"
                :label="__('messages.skb_file')"
                :dataTranslateLabel="'skb_file'"
                :dataTranslateUpload="'click_to_upload'"
                accept="image/png,image/jpeg"
                :required="true"
                :hint="__('messages.file_formats_hint')"
                :dataTranslateHint="'file_formats_hint'"
            />

            {{-- SIM File (Optional) --}}
            <x-file-input
                name="file_sim"
                :label="__('messages.sim_file') . ' (' . __('messages.optional') . ')'"
                :dataTranslateLabel="'sim_file'"
                :dataTranslateUpload="'click_to_upload'"
                accept="image/png,image/jpeg"
                :required="false"
                :hint="__('messages.file_formats_hint')"
                :dataTranslateHint="'file_formats_hint'"
            />
        </div>

        {{-- Role Selection --}}
        <x-select
            name="role"
            :label="__('messages.role')"
            :dataTranslateLabel="'role'"
            :dataTranslateOptions="true"
            :options="[
                ['value' => 'Staff', 'label' => __('messages.role_staff')],
                ['value' => 'Staff Manager', 'label' => __('messages.role_staff_manager')],
                ['value' => 'Lead Manager', 'label' => __('messages.role_lead_manager')],
                ['value' => 'Head Manager', 'label' => __('messages.role_head_manager')],
                ['value' => 'Vice Director', 'label' => __('messages.role_vice_director')],
                ['value' => 'Director', 'label' => __('messages.role_director')]
            ]"
            :required="true"
        />

        {{-- Signature Input --}}
        <x-signature-input
            name="signature"
            :label="__('messages.signature')"
            :dataTranslateLabel="'signature'"
            :required="true"
            :dataTranslateHint="'signature_digital_desc'"
        />

        {{-- Terms and Conditions --}}
        <div class="space-y-1">
            <x-checkbox
                name="terms"
                :label="__('messages.agree_terms')"
                :dataTranslateLabel="'agree_terms'"
                :required="true"
            />
            <div class="ml-7 pl-1">
                <a
                    href="#"
                    @click.prevent="openTermsModal()"
                    class="text-sm text-primary-500 hover:text-primary-600 theme-stylis:text-teal-600 theme-stylis:hover:text-teal-700 underline"
                    data-translate="terms_of_service"
                >
                    {{ __('messages.terms_of_service') }}
                </a>
            </div>
        </div>

        {{-- Submit Button --}}
        <x-button type="submit" variant="primary" size="lg" :fullWidth="true">
            <span data-translate="register">{{ __('messages.register') }}</span>
        </x-button>
    </form>

    {{-- Login Link --}}
    <p class="mt-6 text-center text-sm text-text-secondary">
        <span data-translate="already_have_account">{{ __('messages.already_have_account') }}</span>
        <a href="{{ route('login') }}" class="text-primary-500 hover:text-primary-600 theme-stylis:text-teal-600 theme-stylis:hover:text-teal-700 font-medium" data-translate="login">
            {{ __('messages.login') }}
        </a>
    </p>
</x-login-card>

{{-- Terms of Service Modal --}}
@include('partials.terms-modal')

@once
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
@endpush
@endonce

@once
@push('scripts')
<script>
// Helper function to get translated message
function getToastMessage(key, fallback = '') {
    if (typeof globalLangState !== 'undefined' && globalLangState.translations) {
        return globalLangState.translations[key] || fallback;
    }
    return fallback;
}

// Global function to open terms modal
function openTermsModal() {
    // Wait for Alpine to be ready
    if (typeof Alpine === 'undefined' || !Alpine.store) {
        setTimeout(openTermsModal, 100);
        return;
    }

    // Get form values
    const fullName = document.querySelector('input[name="full_name"]')?.value || '';
    const citizenId = document.querySelector('input[name="citizen_id"]')?.value || '';
    const batch = document.querySelector('input[name="batch"]')?.value || '';

    // Get translated messages
    const msgFillName = getToastMessage('tos_fill_name_first', 'Please fill in your full name first to view the Terms of Service.');
    const msgFillCitizenId = getToastMessage('required_field', 'This field is required') + ' (Citizen ID)';
    const msgFillBatch = getToastMessage('required_field', 'This field is required') + ' (Batch)';

    // Validate all required fields
    if (!fullName || fullName.trim() === '') {
        showToast(msgFillName);
        return;
    }
    if (!citizenId || citizenId.trim() === '') {
        showToast(msgFillCitizenId);
        return;
    }
    if (!batch || batch.trim() === '') {
        showToast(msgFillBatch);
        return;
    }

    // Use Alpine store to open modal
    const termsModalStore = Alpine.store('termsModal');
    if (termsModalStore && termsModalStore.open) {
        termsModalStore.open(fullName, citizenId, batch);
    } else {
        console.error('Terms modal store not found');
        // Fallback: dispatch event
        const event = new CustomEvent('open-terms-modal', {
            detail: { fullName, citizenId, batch }
        });
        window.dispatchEvent(event);
    }
}

// Helper function to show toast
function showToast(message) {
    if (typeof window.$toast !== 'undefined') {
        window.$toast.warning(message);
    } else if (typeof Alpine !== 'undefined' && Alpine.store('toast')) {
        Alpine.store('toast').add(message, 'warning');
    } else {
        alert(message);
    }
}

function registerFormController() {
    return {
        init() {
            // Setup PIN input to only accept numbers
            this.$nextTick(() => {
                const pinInput = document.querySelector('input[name="pin"]');
                if (pinInput) {
                    pinInput.addEventListener('input', (e) => {
                        // Remove any non-numeric characters
                        e.target.value = e.target.value.replace(/[^0-9]/g, '');
                        // Limit to 4 digits
                        if (e.target.value.length > 4) {
                            e.target.value = e.target.value.slice(0, 4);
                        }
                    });

                    // Prevent non-numeric input
                    pinInput.addEventListener('keydown', (e) => {
                        // Allow: backspace, delete, tab, escape, enter
                        if ([8, 9, 27, 13].includes(e.keyCode)) {
                            return;
                        }
                        // Prevent if not a number
                        if (e.key === 'e' || e.key === '-' || e.key === '.' || isNaN(e.key)) {
                            e.preventDefault();
                        }
                    });
                }

                // Setup batch input to only accept numbers
                const batchInput = document.querySelector('input[name="batch"]');
                if (batchInput) {
                    batchInput.addEventListener('keydown', (e) => {
                        // Allow: backspace, delete, tab, escape, enter, arrows
                        if ([8, 9, 27, 13, 37, 38, 39, 40].includes(e.keyCode)) {
                            return;
                        }
                        // Prevent if not a number
                        if (e.key === 'e' || e.key === '-' || e.key === '.' || isNaN(e.key)) {
                            e.preventDefault();
                        }
                    });
                }
            });
        },

        openTermsModal() {
            // Get form values from inputs
            const fullName = document.querySelector('input[name="full_name"]')?.value || '';
            const citizenId = document.querySelector('input[name="citizen_id"]')?.value || '';
            const batch = document.querySelector('input[name="batch"]')?.value || '';

            // Get translated messages
            const msgFillName = getToastMessage('tos_fill_name_first', 'Please fill in your full name first to view the Terms of Service.');
            const msgFillCitizenId = getToastMessage('required_field', 'This field is required') + ' (Citizen ID)';
            const msgFillBatch = getToastMessage('required_field', 'This field is required') + ' (Batch)';

            // Validate all required fields
            if (!fullName || fullName.trim() === '') {
                showToast(msgFillName);
                return;
            }
            if (!citizenId || citizenId.trim() === '') {
                showToast(msgFillCitizenId);
                return;
            }
            if (!batch || batch.trim() === '') {
                showToast(msgFillBatch);
                return;
            }

            // Dispatch event to open terms modal
            window.dispatchEvent(new CustomEvent('open-terms-modal', {
                detail: { fullName, citizenId, batch }
            }));
        }
    };
}

function termsModalController() {
    return {
        isOpen: false,
        currentTab: 'content',
        translations: {},
        formData: {
            fullName: '',
            citizenId: '',
            batch: ''
        },
        generatedPdfBlob: null,

        init() {
            // Get translations from global state
            this.updateTranslations();

            // Store reference to this controller in Alpine store
            Alpine.store('termsModal', {
                open: (fullName, citizenId, batch) => this.open(fullName, citizenId, batch)
            });

            // Listen for language changes
            window.addEventListener('language-changed', () => {
                this.updateTranslations();
                if (this.isOpen) {
                    this.updateContent();
                }
            });

            // Listen for open-terms-modal event (backup method)
            window.addEventListener('open-terms-modal', (e) => {
                const { fullName, citizenId, batch } = e.detail;
                this.open(fullName, citizenId, batch);
            });
        },

        updateTranslations() {
            if (typeof globalLangState !== 'undefined' && globalLangState.translations) {
                this.translations = globalLangState.translations;
            }
        },

        openModal() {
            // Get form values from inputs
            const fullName = document.querySelector('input[name="full_name"]')?.value || '';
            const citizenId = document.querySelector('input[name="citizen_id"]')?.value || '';
            const batch = document.querySelector('input[name="batch"]')?.value || '';

            // Get translated messages
            const msgFillName = this.getLocalizedMessage('tos_fill_name_first', 'Please fill in your full name first to view the Terms of Service.');
            const msgFillCitizenId = this.getLocalizedMessage('required_field', 'This field is required') + ' (Citizen ID)';
            const msgFillBatch = this.getLocalizedMessage('required_field', 'This field is required') + ' (Batch)';

            // Validate all required fields
            if (!fullName || fullName.trim() === '') {
                this.showWarning(msgFillName);
                return;
            }
            if (!citizenId || citizenId.trim() === '') {
                this.showWarning(msgFillCitizenId);
                return;
            }
            if (!batch || batch.trim() === '') {
                this.showWarning(msgFillBatch);
                return;
            }

            this.formData = {
                fullName: fullName,
                citizenId: citizenId,
                batch: batch
            };

            this.isOpen = true;
            this.currentTab = 'content';

            // Wait for modal to open then update content
            this.$nextTick(() => {
                // Add additional delay to ensure DOM is fully rendered
                setTimeout(() => {
                    this.updateContent();
                }, 50);
            });
        },

        open(fullName, citizenId, batch) {
            // Get translated messages
            const msgFillName = this.getLocalizedMessage('tos_fill_name_first', 'Please fill in your full name first to view the Terms of Service.');
            const msgFillCitizenId = this.getLocalizedMessage('required_field', 'This field is required') + ' (Citizen ID)';
            const msgFillBatch = this.getLocalizedMessage('required_field', 'This field is required') + ' (Batch)';

            // Validate all required fields
            if (!fullName || fullName.trim() === '') {
                this.showWarning(msgFillName);
                return;
            }
            if (!citizenId || citizenId.trim() === '') {
                this.showWarning(msgFillCitizenId);
                return;
            }
            if (!batch || batch.trim() === '') {
                this.showWarning(msgFillBatch);
                return;
            }

            this.formData = {
                fullName: fullName,
                citizenId: citizenId,
                batch: batch
            };

            this.isOpen = true;
            this.currentTab = 'content';

            // Wait for modal to open then update content
            this.$nextTick(() => {
                // Add additional delay to ensure DOM is fully rendered
                setTimeout(() => {
                    this.updateContent();
                }, 50);
            });
        },

        close() {
            this.isOpen = false;
            this.currentTab = 'content';
            this.generatedPdfBlob = null;
        },

        getLocalizedMessage(key, fallback = '') {
            if (typeof globalLangState !== 'undefined' && globalLangState.translations) {
                return globalLangState.translations[key] || fallback;
            }
            return fallback;
        },

        showWarning(customMessage = null) {
            const message = customMessage ||
                this.getLocalizedMessage('tos_fill_name_first', 'Please fill in your full name first to view the Terms of Service.');

            // Show toast notification
            if (typeof window.$toast !== 'undefined') {
                window.$toast.warning(message);
            } else if (typeof Alpine !== 'undefined' && Alpine.store('toast')) {
                Alpine.store('toast').add(message, 'warning');
            } else {
                alert(message);
            }
        },

        updateContent() {
            // Update content based on language
            const isIndonesian = globalLangState?.currentLang === 'id';
            const now = new Date();

            // Format date based on language
            const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const timeOptions = { hour: '2-digit', minute: '2-digit' };

            const formattedDate = isIndonesian
                ? now.toLocaleDateString('id-ID', dateOptions)
                : now.toLocaleDateString('en-US', dateOptions);
            const formattedTime = now.toLocaleTimeString('id-ID', timeOptions);

            // Get template content
            const templateWrapper = document.getElementById('tos-content-template');
            const templateContent = document.getElementById('tos-pdf-content');

            if (!templateWrapper || !templateContent) {
                console.error('Template elements not found', { templateWrapper, templateContent });
                return;
            }

            // Helper function to safely update element
            const safeUpdate = (selector, updater) => {
                const el = templateContent.querySelector(selector);
                if (el) updater(el);
                else console.warn(`Element not found: ${selector}`);
            };

            // Update language visibility - show Indonesian, hide English OR vice versa
            templateContent.querySelectorAll('.lang-id').forEach(el => {
                el.style.display = isIndonesian ? '' : 'none';
            });
            templateContent.querySelectorAll('.lang-en').forEach(el => {
                el.style.display = isIndonesian ? 'none' : '';
            });

            // Update form data - update ALL elements with these classes (not just first)
            templateContent.querySelectorAll('.agreement-date').forEach(el => el.textContent = formattedDate);
            templateContent.querySelectorAll('.agreement-time').forEach(el => el.textContent = formattedTime);
            templateContent.querySelectorAll('.agreement-full-name').forEach(el => el.textContent = this.formData.fullName);
            templateContent.querySelectorAll('.agreement-full-name-sign').forEach(el => el.textContent = this.formData.fullName);
            templateContent.querySelectorAll('.agreement-citizen-id').forEach(el => el.textContent = this.formData.citizenId);
            templateContent.querySelectorAll('.agreement-batch').forEach(el => el.textContent = this.formData.batch);

            // Clone to preview - properly clone the content
            const preview = document.getElementById('tos-preview-content');

            if (preview && templateContent) {
                // Clone the entire PDF content div
                const clonedContent = templateContent.cloneNode(true);
                // Remove the ID to avoid duplicate IDs
                clonedContent.removeAttribute('id');
                // Add a class for easier selection later
                clonedContent.classList.add('tos-pdf-content-clone');
                // Remove the hidden class and set proper display
                clonedContent.style.display = 'block';
                clonedContent.style.visibility = 'visible';
                clonedContent.style.position = 'relative';
                // Clear preview and append cloned content
                preview.innerHTML = '';
                preview.appendChild(clonedContent);
                console.log('Content cloned to preview successfully');
            } else {
                console.error('Failed to clone content', { preview, templateContent });
            }
        },

        async generatePDF(event = null) {
            // Check if html2pdf is available
            if (typeof html2pdf === 'undefined') {
                console.error('html2pdf library not loaded');
                const errorMsg = this.getLocalizedMessage('something_went_wrong', 'Something went wrong') + '. PDF library not loaded.';
                alert(errorMsg);
                return;
            }

            console.log('Starting PDF generation...');

            // Update content first to ensure all data is populated
            this.updateContent();

            // Ensure DOM is updated
            await this.$nextTick();
            await new Promise(resolve => setTimeout(resolve, 200));

            // Get the template wrapper and content
            const templateWrapper = document.getElementById('tos-content-template');
            const templateContent = document.getElementById('tos-pdf-content');

            if (!templateWrapper || !templateContent) {
                console.error('Template elements not found', { templateWrapper, templateContent });
                const errorMsg = this.getLocalizedMessage('something_went_wrong', 'Something went wrong') + '. PDF template not found.';
                alert(errorMsg);
                return;
            }

            console.log('Template content found, length:', templateContent.innerHTML.length);

            // Show loading state
            const btn = event && event.target ? event.target : document.querySelector('button[onclick*="generatePDF"]');
            const originalText = btn ? btn.innerHTML : '';

            if (btn) {
                btn.innerHTML = '<svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
                btn.disabled = true;
            }

            try {
                const isIndonesian = globalLangState?.currentLang === 'id';
                const filename = isIndonesian
                    ? `surat_pernyataan_${this.formData.citizenId || 'user'}.pdf`
                    : `statement_letter_${this.formData.citizenId || 'user'}.pdf`;

                // Store original styles
                const wrapperOriginalDisplay = templateWrapper.style.display;
                const contentOriginalDisplay = templateContent.style.display;
                const contentOriginalVisibility = templateContent.style.visibility;

                // Make wrapper and content visible temporarily for html2pdf
                templateWrapper.style.display = 'block';
                templateWrapper.style.visibility = 'visible';
                templateWrapper.classList.remove('hidden');
                templateContent.style.display = 'block';
                templateContent.style.visibility = 'visible';
                templateContent.style.position = 'relative';

                console.log('Made template visible for PDF generation');

                const opt = {
                    margin: 10,
                    filename: filename,
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: {
                        scale: 2,
                        useCORS: true,
                        letterRendering: true,
                        backgroundColor: '#ffffff',
                        logging: true,
                        scrollX: 0,
                        scrollY: 0,
                        onclone: (clonedDoc) => {
                            console.log('html2canvas onclone called');
                            // Find and style the cloned element
                            const clonedWrapper = clonedDoc.querySelector('#tos-content-template');
                            const clonedElement = clonedDoc.querySelector('.tos-pdf-content');
                            if (clonedWrapper) {
                                clonedWrapper.style.display = 'block';
                                clonedWrapper.style.visibility = 'visible';
                                clonedWrapper.style.position = 'static';
                                clonedWrapper.style.left = '0';
                                clonedWrapper.style.top = '0';
                                clonedWrapper.style.margin = '0';
                                clonedWrapper.style.padding = '0';
                                clonedWrapper.style.width = 'auto';
                                clonedWrapper.style.height = 'auto';
                            }
                            if (clonedElement) {
                                console.log('Found cloned element, applying styles');
                                // Get computed dimensions before changing styles
                                const rect = clonedElement.getBoundingClientRect();
                                console.log('Element dimensions:', rect.width, 'x', rect.height);

                                clonedElement.style.display = 'block';
                                clonedElement.style.visibility = 'visible';
                                clonedElement.style.position = 'static';
                                clonedElement.style.left = '0';
                                clonedElement.style.top = '0';
                                clonedElement.style.right = 'auto';
                                clonedElement.style.bottom = 'auto';
                                clonedElement.style.transform = 'none';
                                clonedElement.style.letterSpacing = 'normal';
                                clonedElement.style.wordSpacing = 'normal';
                                clonedElement.style.textSpacing = 'normal';
                                clonedElement.style.minHeight = 'auto';
                                clonedElement.style.height = 'auto';
                                clonedElement.style.width = '100%';
                                clonedElement.style.maxWidth = '100%';
                                clonedElement.style.padding = '25px';
                                clonedElement.style.margin = '0';
                                clonedElement.style.overflow = 'visible';
                                clonedElement.style.boxSizing = 'border-box';

                                // Fix all children with absolute positioning (except logo background)
                                const allChildren = clonedElement.querySelectorAll('*');
                                allChildren.forEach(child => {
                                    const style = window.getComputedStyle(child);
                                    if (style.position === 'absolute') {
                                        // Check if it's the logo background
                                        const isLogoBg = child.querySelector('img') && style.opacity === '0.05';
                                        if (!isLogoBg) {
                                            child.style.position = 'static';
                                        }
                                    }
                                });
                            } else {
                                console.error('Cloned element not found!');
                            }
                        }
                    },
                    jsPDF: {
                        unit: 'mm',
                        format: 'a4',
                        orientation: 'portrait'
                    }
                };

                console.log('Generating PDF...');
                console.log('Content length:', templateContent.innerHTML.length);

                // Generate PDF
                const pdfWorker = html2pdf().set(opt).from(templateContent);
                this.generatedPdfBlob = await pdfWorker.output('blob');

                console.log('PDF generated successfully, size:', this.generatedPdfBlob.size);

                // Restore original styles
                templateWrapper.style.display = wrapperOriginalDisplay;
                templateWrapper.style.visibility = '';
                templateContent.style.display = contentOriginalDisplay;
                templateContent.style.visibility = contentOriginalVisibility;

                // Create preview URL
                const pdfUrl = URL.createObjectURL(this.generatedPdfBlob);
                const frame = document.getElementById('pdf-preview-frame');
                if (frame) {
                    frame.src = pdfUrl;
                    console.log('PDF preview loaded in iframe');
                }

                this.currentTab = 'preview';
            } catch (error) {
                console.error('PDF generation error:', error);
                const errorMsg = this.getLocalizedMessage('something_went_wrong', 'Something went wrong') + ': ' + error.message;
                alert(errorMsg);
                // Restore visibility on error too
                if (templateWrapper && templateContent) {
                    templateWrapper.style.display = wrapperOriginalDisplay;
                    templateWrapper.style.visibility = '';
                    templateContent.style.display = contentOriginalDisplay;
                    templateContent.style.visibility = contentOriginalVisibility;
                }
            } finally {
                if (btn) {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            }
        },

        downloadPDF() {
            const isIndonesian = globalLangState?.currentLang === 'id';
            const filename = isIndonesian
                ? `surat_pernyataan_${this.formData.citizenId || 'user'}.pdf`
                : `statement_letter_${this.formData.citizenId || 'user'}.pdf`;

            if (!this.generatedPdfBlob) {
                this.generatePDF().then(() => {
                    if (this.generatedPdfBlob) {
                        const url = URL.createObjectURL(this.generatedPdfBlob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = filename;
                        a.click();
                        URL.revokeObjectURL(url);
                    }
                });
                return;
            }

            const url = URL.createObjectURL(this.generatedPdfBlob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            a.click();
            URL.revokeObjectURL(url);
        }
    };
}

document.addEventListener('alpine:init', () => {
    Alpine.data('registerFormController', registerFormController);
    Alpine.data('termsModalController', termsModalController);

    // Initialize terms modal store (will be populated when modal is created)
    Alpine.store('termsModal', {
        open: () => {
            console.warn('Terms modal not ready yet, please try again');
        }
    });
});

</script>
@endpush
@endonce

@once
@push('styles')
<style>
.tos-pdf-content {
    background: white !important;
    color: #000 !important;
}

/* Make sure template can be made visible for PDF generation */
#tos-content-template {
    display: none;
}

#tos-content-template.force-visible {
    display: block !important;
    visibility: visible !important;
}

@media print {
    .tos-pdf-content {
        width: 210mm;
        min-height: 297mm;
        margin: 0;
        padding: 15mm;
    }
}
</style>
@endpush
@endonce

@endsection
