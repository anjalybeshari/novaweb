import { Component }          from '@angular/core';
import { ApiService }         from '../../services/api.service';
import { CommonModule }       from '@angular/common';
import { FormsModule }        from '@angular/forms';
import { NavbarComponent }    from '../navbar/navbar.component';

@Component({
  selector: 'app-search',
  standalone: true,
  imports: [CommonModule, FormsModule, NavbarComponent],
  templateUrl: './search.component.html'
})
export class SearchComponent {
  query = '';
  results: any[] = [];

  constructor(private api: ApiService) {}

  submit() {
    this.api.search(this.query).subscribe(data => this.results = data);
  }
}
