import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { OrderService } from '../../services/order'; // Asegúrate de tener este servicio

@Component({
    selector: 'app-pedido-detalle',
    standalone: true,
    imports: [CommonModule, RouterLink],
    template: `
    <div class="max-w-4xl mx-auto p-8">
      @if (loading) {
        <div class="flex justify-center py-20"><div class="animate-spin rounded-full h-12 w-12 border-t-2 border-friki-neon"></div></div>
      } @else if (pedido) {
        <div class="mb-8 flex justify-between items-end">
          <div>
            <h1 class="text-3xl font-black font-orbitron text-white uppercase">Pedido {{pedido.numero_pedido}}</h1>
            <p class="text-slate-400">Realizado el {{pedido.created_at | date:'dd/MM/yyyy HH:mm'}}</p>
          </div>
          <span class="px-4 py-2 rounded-full font-black text-xs uppercase border border-friki-neon text-friki-neon">
            {{ pedido.estado }}
          </span>
        </div>

        <div class="bg-friki-card border border-slate-800 rounded-3xl overflow-hidden">
          <table class="w-full text-left">
            <thead>
              <tr class="bg-slate-900/50 border-b border-slate-800 text-xs text-slate-500 uppercase">
                <th class="px-6 py-4">Producto</th>
                <th class="px-6 py-4 text-center">Cant.</th>
                <th class="px-6 py-4 text-right">Subtotal</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
            @for (item of pedido.detalles; track item.id) {
                <tr class="text-sm">
                <td class="px-6 py-4 text-white font-bold">
                    {{ item.producto.nombre }}
                </td>
                <td class="px-6 py-4 text-center text-slate-300">{{ item.cantidad }}</td>
                <td class="px-6 py-4 text-right text-friki-neon font-bold">{{ item.subtotal }}€</td>
                </tr>
            }
            </tbody>
            <tfoot class="bg-slate-900/30">
              <tr>
                <td colspan="2" class="px-6 py-4 text-right font-bold text-slate-400">TOTAL</td>
                <td class="px-6 py-4 text-right font-black text-xl text-white">{{ pedido.total }}€</td>
              </tr>
            </tfoot>
          </table>
        </div>
        
        <div class="mt-8 text-center">
            <a routerLink="/perfil" class="text-slate-500 hover:text-white transition-colors underline">Volver al historial</a>
        </div>
      }
    </div>
  `
})
export class PedidoDetalleComponent implements OnInit {
    private route = inject(ActivatedRoute);
    private orderService = inject(OrderService);
    pedido: any;
    loading = true;

    ngOnInit() {
        const id = this.route.snapshot.paramMap.get('id');
        if (id) {
            this.orderService.getPedido(id).subscribe({
                next: (data) => {
                    console.log('Estructura completa del pedido:', data);
                    this.pedido = data;
                    this.loading = false;
                },
                error: (err) => {
                    console.error('Error cargando el pedido específico:', err);
                    this.loading = false;
                }
            });
        }
    }
}