import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, RouterLink } from '@angular/router';

@Component({
  selector: 'app-order-success',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './order-success.html',
  styleUrls: ['./order-success.css']
})
export class OrderSuccessComponent implements OnInit {
  order: any = null;
  private router = inject(Router);

  ngOnInit() {
    this.order = history.state.order;
    if (!this.order) {
      this.router.navigate(['/']);
    }
  }
}
