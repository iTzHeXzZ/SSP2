@extends('layouts.app')

@section('content')
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header">
            <h3 class="card-title">Formular</h3>
        </div>
        <div class="card-body" x-data="formHandler()">
            <form method="POST" action="{{ route('dg.submit') }}">
                @csrf
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" class="form-control" name="name" placeholder="Name" x-model="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" class="form-control" name="email" placeholder="Email" x-model="email" required>
                </div>
                <div class="form-group">
                    <label for="tarif">Tarif</label>
                    <select id="tarif" class="form-control" name="tarif" x-model="tarif" required>
                        <option value="">Bitte wählen</option>
                        <option value="DG Giga 12M">DG Giga 12M</option>
                        <option value="DG Giga">DG Giga</option>
                        <option value="DG Premium 500">DG Premium 500</option>
                        <option value="DG Classic 300">DG Classic 300</option>
                        <option value="DG Basic 100">DG Basic 100</option>
                        <option value="DG Universal">DG Universal</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="signature">Unterschrift</label>
                    <canvas id="signatureCanvas" class="border" width="400" height="250" style="width: 100%; max-width: 400px; max-height: 250px" x-ref="signatureCanvas"></canvas>
                    <input type="hidden" name="unterschrift" x-model="unterschrift">
                    <button type="button" class="btn btn-secondary mt-2" @click="clearSignature()">Signatur löschen</button>
                </div>
                <button type="submit" class="btn btn-primary">Absenden</button>
            </form>
        </div>
    </div>
</div>

<script>
    function formHandler() {
        return {
            name: '',
            email: '',
            tarif: '',
            isDrawing: false,
            x: 0,
            y: 0,
            unterschrift: '',
            canvas: null,
            ctx: null,
            init() {
                this.canvas = this.$refs.signatureCanvas;
                this.ctx = this.canvas.getContext('2d');
                this.ctx.strokeStyle = 'rgb(100, 149, 237)';
                this.ctx.lineWidth = 2;
                this.canvas.addEventListener('mousedown', this.startDrawing.bind(this));
                this.canvas.addEventListener('mousemove', this.draw.bind(this));
                this.canvas.addEventListener('mouseup', this.stopDrawing.bind(this));
                this.canvas.addEventListener('mouseout', this.stopDrawing.bind(this));
            },
            startDrawing(event) {
                this.isDrawing = true;
                this.updateCoordinates(event);
            },
            draw(event) {
                if (!this.isDrawing) return;
                this.ctx.beginPath();
                this.ctx.moveTo(this.x, this.y);
                this.updateCoordinates(event);
                this.ctx.lineTo(this.x, this.y);
                this.ctx.stroke();
                this.unterschrift = this.canvas.toDataURL();
            },
            stopDrawing() {
                this.isDrawing = false;
            },
            updateCoordinates(event) {
                const rect = this.canvas.getBoundingClientRect();
                this.x = event.clientX - rect.left;
                this.y = event.clientY - rect.top;
            },
            clearSignature() {
                this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                this.signature = '';
            }
        }
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('formHandler', formHandler);
    });
</script>
@endsection
