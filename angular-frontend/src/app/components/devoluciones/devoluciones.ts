import { Component } from '@angular/core';

@Component({
  selector: 'app-devoluciones',
  standalone: true,
  template: `
    <div class="p-8 max-w-4xl mx-auto text-slate-300">
      <h1 class="text-4xl font-black font-orbitron text-white uppercase tracking-widest mb-8 border-l-4 border-friki-neon pl-4">
        Política de <span class="text-friki-neon">Devoluciones</span>
      </h1>

      <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-8 shadow-2xl space-y-8">
        
        <section>
          <h2 class="text-xl font-bold text-white mb-3">1. ¿Cambio de opinión?</h2>
          <p>En nuestra tienda, queremos que tu pedido sea perfecto. Tienes un plazo de <strong>14 días naturales</strong> desde la recepción del paquete para devolver cualquier producto que no haya cumplido tus expectativas.</p>
        </section>

        <section>
          <h2 class="text-xl font-bold text-white mb-3">2. Estado del objeto</h2>
          <p>Para poder procesar la devolución, el objeto debe estar en su estado original, sin señales de uso (¡nada de abrir las cajas de figuras de colección!) y con su embalaje intacto.</p>
        </section>

        <section>
          <h2 class="text-xl font-bold text-white mb-3">3. ¿Cómo iniciar la misión?</h2>
          <p>Envíanos un mensaje a <a href="mailto:soporte@frikistore.com" class="text-friki-neon hover:underline">admin@frikistore.com</a> indicando tu número de pedido y el motivo de la devolución. Nuestro equipo de soporte te responderá en menos de 24 horas laborables.</p>
        </section>

        <section>
          <h2 class="text-xl font-bold text-white mb-3">4. Reembolsos</h2>
          <p>Una vez recibido y verificado el objeto en nuestro almacén, procesaremos el reembolso mediante el mismo método de pago utilizado en la compra. El proceso puede tardar entre 5 y 10 días laborables dependiendo de tu entidad bancaria.</p>
        </section>

        <div class="pt-6 border-t border-slate-800">
          <p class="text-sm italic text-slate-500">Nota: Los productos personalizados o artículos de higiene abiertos no podrán ser devueltos por seguridad y salud. Los Juegos de Cartas Coleccionables no podrán ser devueltos dada su naturaleza.</p>
        </div>
      </div>
    </div>
  `
})
export class DevolucionesComponent {}