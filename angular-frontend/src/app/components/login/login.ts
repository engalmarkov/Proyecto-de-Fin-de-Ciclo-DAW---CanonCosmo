// src/app/components/login/login.ts
import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, RouterLink } from '@angular/router';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { AuthService } from '../../services/auth'; // Asegúrate de que la ruta al servicio sea correcta

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, RouterLink, ReactiveFormsModule],
  templateUrl: './login.html',
  styleUrls: ['./login.css']
})
export class LoginComponent { // <--- ESTO es lo que busca el app.routes.ts
  private fb = inject(FormBuilder);
  private authService = inject(AuthService);
  private router = inject(Router);
  public user = this.authService.currentUser;

  loginForm: FormGroup = this.fb.group({
    username: ['', [Validators.required, Validators.email]],
    password: ['', [Validators.required, Validators.minLength(4)]]
  });

  error: string | null = null;
  loading = false;

onSubmit() {
    if (this.loginForm.valid) {
      this.loading = true;
      
      this.authService.login(this.loginForm.value).subscribe({
        next: () => {
          this.router.navigate(['/perfil']);
        },
        error: () => {
          this.error = 'Credenciales incorrectas. ¿Has olvidado tu contraseña de la nave?';
          this.loading = false;
        }
      });
    }
  }
}