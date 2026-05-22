import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CalendarioJuegos } from './calendario-juegos';

describe('CalendarioJuegos', () => {
  let component: CalendarioJuegos;
  let fixture: ComponentFixture<CalendarioJuegos>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CalendarioJuegos],
    }).compileComponents();

    fixture = TestBed.createComponent(CalendarioJuegos);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
