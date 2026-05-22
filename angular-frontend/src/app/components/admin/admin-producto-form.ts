import { Component, inject, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { ProductService } from '../../services/product';

@Component({
  selector: 'app-admin-producto-form',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterLink],
  template: `
    <div class="p-8 max-w-3xl mx-auto">
      <div class="mb-8">
        <h1 class="text-3xl font-black font-orbitron text-white uppercase tracking-widest">
          {{ isEditMode ? 'ACTUALIZAR' : 'FORJAR NUEVO' }} <span class="text-friki-neon">OBJETO</span>
        </h1>
      </div>

      <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-8 shadow-2xl">
        <form [formGroup]="productoForm" (ngSubmit)="guardarLoot()">
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Campo Nombre -->
            <div class="col-span-1 md:col-span-2">
              <label class="block text-slate-400 font-bold mb-2 uppercase text-sm tracking-wider">Nombre del Objeto</label>
              <input 
                type="text" 
                formControlName="nombre"
                class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-3 focus:outline-none focus:border-friki-neon focus:ring-1 focus:ring-friki-neon transition-all"
                placeholder="Ej: Espada Maestra, Poción de Maná..."
              >
              <div *ngIf="productoForm.get('nombre')?.invalid && productoForm.get('nombre')?.touched" class="text-red-500 text-sm mt-2 font-bold">
                ¡Todo objeto necesita un nombre épico!
              </div>
            </div>

            <!-- Campo Precio -->
            <div>
              <label class="block text-slate-400 font-bold mb-2 uppercase text-sm tracking-wider">Valor en Oro (€)</label>
              <input 
                type="number" 
                formControlName="precio"
                class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-3 focus:outline-none focus:border-friki-neon focus:ring-1 focus:ring-friki-neon transition-all"
                placeholder="0.00"
                step="0.01"
              >
              <div *ngIf="productoForm.get('precio')?.invalid && productoForm.get('precio')?.touched" class="text-red-500 text-sm mt-2 font-bold">
                El precio debe ser mayor o igual a 0.
              </div>
            </div>

            <!-- Campo Stock -->
            <div>
              <label class="block text-slate-400 font-bold mb-2 uppercase text-sm tracking-wider">Stock (Unidades)</label>
              <input 
                type="number" 
                formControlName="stock"
                class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-3 focus:outline-none focus:border-friki-neon focus:ring-1 focus:ring-friki-neon transition-all"
                placeholder="0"
              >
              <div *ngIf="productoForm.get('stock')?.invalid && productoForm.get('stock')?.touched" class="text-red-500 text-sm mt-2 font-bold">
                El stock no puede ser negativo.
              </div>
            </div>

            <!-- Campo Categoría (ID) -->
            <div>
              <label class="block text-slate-400 font-bold mb-2 uppercase text-sm tracking-wider">ID de la Categoría</label>
              <input 
                type="number" 
                formControlName="categoria_id"
                class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-3 focus:outline-none focus:border-friki-neon focus:ring-1 focus:ring-friki-neon transition-all"
                placeholder="Ej: 1"
              >
              <div *ngIf="productoForm.get('categoria_id')?.invalid && productoForm.get('categoria_id')?.touched" class="text-red-500 text-sm mt-2 font-bold">
                Debes asignar una categoría válida.
              </div>
            </div>

            <!-- Campo Imagen URL -->
            <div>
              <label class="block text-slate-400 font-bold mb-2 uppercase text-sm tracking-wider">URL de la Imagen</label>
              <input 
                type="text" 
                formControlName="imagen_url"
                class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-3 focus:outline-none focus:border-friki-neon focus:ring-1 focus:ring-friki-neon transition-all"
                placeholder="https://..."
              >
            </div>
          </div>

          <!-- Campo Descripción -->
          <div class="mb-8">
            <label class="block text-slate-400 font-bold mb-2 uppercase text-sm tracking-wider">Descripción del Lore</label>
            <textarea 
              formControlName="descripcion"
              rows="4"
              class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-3 focus:outline-none focus:border-friki-neon focus:ring-1 focus:ring-friki-neon transition-all resize-none"
              placeholder="Cuenta la historia y atributos de este objeto..."
            ></textarea>
            <div *ngIf="productoForm.get('descripcion')?.invalid && productoForm.get('descripcion')?.touched" class="text-red-500 text-sm mt-2 font-bold">
              Una buena descripción es obligatoria.
            </div>
          </div>

          <!-- Botonera -->
          <div class="flex justify-end space-x-4">
            <a routerLink="/admin/productos" class="px-6 py-3 rounded-xl text-slate-400 font-bold hover:text-white hover:bg-slate-800 transition-colors">
              Cancelar Misión
            </a>
            <button 
              type="submit" 
              [disabled]="productoForm.invalid || loading"
              class="bg-friki-neon text-slate-900 font-bold py-3 px-6 rounded-xl hover:bg-white transition-all transform active:scale-95 shadow-[0_0_15px_rgba(0,255,255,0.4)] disabled:opacity-50 disabled:cursor-not-allowed">
              <i class="fa-solid" [ngClass]="isEditMode ? 'fa-save' : 'fa-hammer'"></i> 
              {{ isEditMode ? 'Guardar Cambios' : 'Crear Objeto' }}
            </button>
          </div>

        </form>
      </div>
    </div>
  `
})
export class AdminProductoFormComponent implements OnInit {
  private fb = inject(FormBuilder);
  private productService = inject(ProductService);
  private router = inject(Router);
  private route = inject(ActivatedRoute);

  productoForm!: FormGroup;
  isEditMode = false;
  productoId: number | null = null;
  loading = false;

  ngOnInit() {
    this.productoForm = this.fb.group({
      nombre: ['', Validators.required],
      precio: [0, [Validators.required, Validators.min(0)]],
      descripcion: ['', Validators.required],
      stock: [0, [Validators.required, Validators.min(0)]],
      categoria_id: [1, Validators.required], 
      imagen_url: [''] 
    });

    this.route.paramMap.subscribe(params => {
      const id = params.get('id');
      if (id) {
        this.isEditMode = true;
        this.productoId = +id;
        this.cargarDatosObjeto(this.productoId);
      }
    });
  }

  cargarDatosObjeto(id: number) {
    this.loading = true;
    this.productService.getProduct(id).subscribe({
      next: (data: any) => {
        const catId = data.categoria ? data.categoria.id : 1;

        this.productoForm.patchValue({
          nombre: data.nombre,
          precio: data.precio,
          descripcion: data.descripcion,
          stock: data.stock,
          categoria_id: catId,
          imagen_url: data.imagen_url
        });
        this.loading = false;
      },
      error: (err) => {
        console.error('Error al invocar los datos del objeto', err);
        this.loading = false;
        this.router.navigate(['/admin/productos']);
      }
    });
  }

  guardarLoot() {
    if (this.productoForm.invalid) {
      // Forzamos a que se muestren los errores si intentan enviar vacío
      this.productoForm.markAllAsTouched();
      return;
    }

    this.loading = true;
    const datosLoot = this.productoForm.value;

    if (this.isEditMode && this.productoId) {
      // ACTUALIZAR (Update)
      this.productService.updateProduct(this.productoId, datosLoot).subscribe({
        next: () => {
          this.router.navigate(['/admin/productos']);
        },
        error: (err) => {
          console.error('Fallo crítico al actualizar', err);
          alert('Error al actualizar. Revisa la consola o la terminal de Symfony.');
          this.loading = false;
        }
      });
    } else {
      // CREAR (Create)
      this.productService.createProduct(datosLoot).subscribe({
        next: () => {
          this.router.navigate(['/admin/productos']);
        },
        error: (err) => {
          console.error('Fallo crítico al forjar', err);
          alert('Error al forjar el objeto. Revisa la consola o la terminal de Symfony.');
          this.loading = false;
        }
      });
    }
  }
}