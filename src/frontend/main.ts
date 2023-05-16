import { platformBrowserDynamic } from '@angular/platform-browser-dynamic';

import { AppModule } from './app/app.module';

sessionStorage.setItem('playing', String(false));

platformBrowserDynamic().bootstrapModule(AppModule)
  .catch(err => console.error(err));
