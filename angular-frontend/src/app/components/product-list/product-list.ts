import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { ProductService, Producto } from '../../services/product';
import { CartService } from '../../services/cart';

@Component({
  selector: 'app-product-list',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './product-list.html',
  styleUrl: './product-list.css'
})
export class ProductListComponent implements OnInit {
  private productService = inject(ProductService);
  private route = inject(ActivatedRoute);
  private cartService = inject(CartService);

  productos: Producto[] = [];
  categoriaActual: string = 'Todos';
  loading: boolean = true;

  categorias = ['Todos', 'Videojuegos', 'Anime & Manga', 'Cómics', 'Juegos de cartas', 'Figuras', 'Ropa'];

  // --- VARIABLES PARA EL TOAST ---
  mostrarToast: boolean = false;
  mensajeToast: string = '';

  ngOnInit(): void {
    this.route.queryParams.subscribe(params => {
      // 1. Detectamos si vienen los filtros especiales desde el footer o navbar
      const esOferta = params['ofertas'] === 'true';
      const esReserva = params['reservas'] === 'true';
      const esMasVendido = params['mas_vendidos'] === 'true';
      const terminoBusqueda = params['buscar'];

      // 2. Si no hay categoría en la URL, por defecto es 'Todos'
      this.categoriaActual = params['categoria'] || 'Todos';

      // 3. Truco visual: Cambiamos el título h2 de la página si es un filtro especial
      if (esOferta) this.categoriaActual = 'OFERTAS FLASH';
      if (esReserva) this.categoriaActual = 'PRÓXIMAMENTE';
      if (esMasVendido) this.categoriaActual = 'MÁS VENDIDOS';

      // Si hay búsqueda, mostramos qué estamos buscando
      if (terminoBusqueda) this.categoriaActual = `BÚSQUEDA: ${terminoBusqueda.toUpperCase()}`;

      // 4. Llamamos a cargar los productos pasándole todos los filtros
      this.loadProducts(esOferta, esReserva, esMasVendido, terminoBusqueda);
    });
  }

  loadProducts(ofertas: boolean, reservas: boolean, masVendidos: boolean, buscar?: string): void {
    this.loading = true;

    // Si hay algún filtro activo, pedimos 'Todos' los productos para filtrarlos nosotros en el frontend
    const categoriaParaServicio = (ofertas || reservas || masVendidos || buscar) ? 'Todos' : this.categoriaActual;

    this.productService.getProducts(categoriaParaServicio).subscribe({
      next: (data) => {
        let productosFiltrados = data.productos;

        // --- APLICAMOS LOS FILTROS FRONTEND ---

        // NUEVO FILTRO: Búsqueda por nombre
        if (buscar) {
          productosFiltrados = productosFiltrados.filter((p: Producto) =>
            p.nombre.toLowerCase().includes(buscar.toLowerCase())
          );
        }

        // Filtro: Ofertas Flash
        if (ofertas) {
          productosFiltrados = productosFiltrados.filter((p: any) => p.precioOferta || p.precio_oferta);
        }

        // Guardamos los productos ya filtrados para que se pinten en el HTML
        this.productos = productosFiltrados;
        this.loading = false;
      },
      error: (err) => {
        console.error('Error cargando productos', err);
        this.loading = false;
      }
    });
  }

  agregarAlCarrito(producto: any, event?: Event) {
    // Si pasamos el evento, evitamos que el clic se propague y nos cambie de página
    if (event) {
      event.stopPropagation();
      event.preventDefault();
    }

    // 1. Enviamos el loot a la función addToCart de tu servicio
    this.cartService.addToCart(producto);

    // 2. Preparamos el mensaje visual
    this.mensajeToast = producto.nombre;
    this.mostrarToast = true;

    // 3. Lo ocultamos tras 3 segundos
    setTimeout(() => {
      this.mostrarToast = false;
    }, 3000);
  }
}