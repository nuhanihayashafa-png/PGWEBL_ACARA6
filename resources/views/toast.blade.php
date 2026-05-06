{{-- ══════ SUCCESS TOAST ══════ --}}
@if (session()->has('success'))
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 99999;">
        <div id="liveToastSuccess" class="toast align-items-center border-0" role="alert" aria-live="assertive"
            aria-atomic="true"
            style="background:#2E7D32;color:#fff;font-family:'Lora',serif;min-width:300px;border-radius:10px;box-shadow:0 4px 16px rgba(0,0,0,0.25)">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <span style="font-size:1.2rem">✅</span>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
@endif

{{-- ══════ ERROR TOAST ══════ --}}
@if (session()->has('error'))
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 99999;">
        <div id="liveToastError" class="toast align-items-center border-0" role="alert" aria-live="assertive"
            aria-atomic="true"
            style="background:#c0392b;color:#fff;font-family:'Lora',serif;min-width:300px;border-radius:10px;box-shadow:0 4px 16px rgba(0,0,0,0.25)">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <span style="font-size:1.2rem">❌</span>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
@endif

{{-- ══════ WARNING TOAST ══════ --}}
@if (session()->has('warning'))
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 99999;">
        <div id="liveToastWarning" class="toast align-items-center border-0" role="alert" aria-live="assertive"
            aria-atomic="true"
            style="background:#C8860A;color:#fff;font-family:'Lora',serif;min-width:300px;border-radius:10px;box-shadow:0 4px 16px rgba(0,0,0,0.25)">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <span style="font-size:1.2rem">⚠️</span>
                    <span>{{ session('warning') }}</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
@endif

{{-- ══════ VALIDATION ERROR TOAST ══════ --}}
@if ($errors->any())
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 99999;">
        <div id="liveToastValidation" class="toast align-items-center border-0" role="alert" aria-live="assertive"
            aria-atomic="true"
            style="background:#c0392b;color:#fff;font-family:'Lora',serif;min-width:300px;border-radius:10px;box-shadow:0 4px 16px rgba(0,0,0,0.25)">
            <div class="d-flex">
                <div class="toast-body">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span style="font-size:1.2rem">❌</span>
                        <strong>Validasi gagal:</strong>
                    </div>
                    @foreach ($errors->all() as $error)
                        <div style="font-size:0.82rem;margin-left:1.8rem">• {{ $error }}</div>
                    @endforeach
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
@endif

{{-- ══════ INISIALISASI SEMUA TOAST ══════ --}}
@if (session()->has('success') || session()->has('error') || session()->has('warning') || $errors->any())
    <script>
        // Tunggu Bootstrap benar-benar siap
        document.addEventListener('DOMContentLoaded', function() {
            var ids = ['liveToastSuccess', 'liveToastError', 'liveToastWarning', 'liveToastValidation'];
            ids.forEach(function(id) {
                var el = document.getElementById(id);
                if (el) {
                    var t = new bootstrap.Toast(el, {
                        delay: 5000
                    });
                    t.show();
                }
            });
        });
    </script>
@endif
