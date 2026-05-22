import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';

@Component({
  selector: 'app-contacto',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  template: `
    <div class="p-8 max-w-2xl mx-auto">
      <h1 class="text-4xl font-black font-orbitron text-white uppercase tracking-widest mb-2 text-center">
        Contacto <span class="text-friki-neon">Directo</span>
      </h1>
      <p class="text-slate-400 text-center mb-10">¿Tienes dudas sobre un pedido o quieres pedir un objeto de colección especial? Escríbenos.</p>

      <form [formGroup]="contactoForm" (ngSubmit)="enviarMensaje()" class="bg-slate-900/60 border border-slate-800 rounded-2xl p-8 shadow-2xl">
        
        <div class="mb-6">
          <label class="block text-slate-400 font-bold mb-2 uppercase text-xs tracking-wider">Tu Nombre</label>
          <input type="text" formControlName="nombre" class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-3 focus:outline-none focus:border-friki-neon focus:ring-1 focus:ring-friki-neon transition-all">
        </div>

        <div class="mb-6">
          <label class="block text-slate-400 font-bold mb-2 uppercase text-xs tracking-wider">Email de contacto</label>
          <input type="email" formControlName="email" class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-3 focus:outline-none focus:border-friki-neon focus:ring-1 focus:ring-friki-neon transition-all">
        </div>

        <div class="mb-8">
          <label class="block text-slate-400 font-bold mb-2 uppercase text-xs tracking-wider">Mensaje</label>
          <textarea formControlName="mensaje" rows="5" class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-3 focus:outline-none focus:border-friki-neon focus:ring-1 focus:ring-friki-neon transition-all resize-none"></textarea>
        </div>

        <button type="submit" [disabled]="contactoForm.invalid" class="w-full bg-friki-neon text-slate-900 font-bold py-4 rounded-xl hover:bg-white transition-all transform active:scale-95 disabled:opacity-50 uppercase tracking-widest shadow-[0_0_15px_rgba(0,255,255,0.4)]">
          Enviar Mensaje
        </button>
      </form>
    </div>
  `
})
export class ContactoComponent {
  private fb = inject(FormBuilder);
  contactoForm: FormGroup;

  constructor() {
    this.contactoForm = this.fb.group({
      nombre: ['', Validators.required],
      email: ['', [Validators.required, Validators.email]],
      mensaje: ['', Validators.required]
    });
  }

  enviarMensaje() {
    if (this.contactoForm.valid) {
      console.log('Mensaje enviado:', this.contactoForm.value);
      alert('¡Mensaje enviado a la Base Rebelde! Te responderemos en menos de 24h.');
      this.contactoForm.reset();
    }
  }
}