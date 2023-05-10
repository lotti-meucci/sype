import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { NicknameResponse } from 'app/interfaces/nickname-response';
import { CredentialsRequest } from 'app/interfaces/credentials-request';
import { Observable } from 'rxjs';

const CONFIG = { withCredentials: true };

@Injectable({
  providedIn: 'root'
})
export class SypeApiService {
  constructor(private http: HttpClient) { }

  getLogin(): Observable<NicknameResponse> {
    return this.http.get<NicknameResponse>('/login.php', CONFIG);
  }

  postLogin(credentials: CredentialsRequest): Observable<unknown> {
    return this.http.post('/login.php', credentials, CONFIG);
  }

  putUser(credentials: CredentialsRequest): Observable<unknown> {
    return this.http.put('/users.php', credentials, CONFIG);
  }
}
