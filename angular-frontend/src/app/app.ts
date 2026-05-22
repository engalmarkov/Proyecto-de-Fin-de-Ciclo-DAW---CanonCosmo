import { Component } from '@angular/core';
import { RouterOutlet, RouterLink } from '@angular/router';
import { NavbarComponent } from './components/navbar/navbar'; // <--- Cambiado aquí

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [
    RouterOutlet, 
    RouterLink, 
    NavbarComponent // <--- Y cambiado aquí
  ],
  templateUrl: './app.html',
  styleUrl: './app.css'
})
export class App {
  title = 'canoncosmo-app';
}