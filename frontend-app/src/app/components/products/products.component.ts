
import { CommonModule }      from '@angular/common';
import { NavbarComponent }   from '../navbar/navbar.component';
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { ApiService } from '../../services/api.service';
import { BrowserModule } from '@angular/platform-browser';
import { HttpClientModule } from '@angular/common/http';

@Component({
  selector: 'app-products',
  templateUrl: './products.component.html',
  imports:[NavbarComponent,CommonModule, HttpClientModule,RouterModule],
   
})
export class ProductsComponent implements OnInit {
  products:any[]=[];

  constructor(
    private route: ActivatedRoute,
    private api: ApiService
  ) {}

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.api.getProducts(id).subscribe({
      next: (data) => {
        this.products = data;
      },
      error: (err) => {
        console.error('Failed to load product', err);
      }
    });
  }
}
