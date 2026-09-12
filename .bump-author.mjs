import { readFileSync, writeFileSync, readdirSync, statSync } from 'node:fs';
import { join, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = 'e:/Github/WPCleanAdmin/wpcleanadmin';
const NEW_VERSION = '1.8.5';

function walk(dir, out = []) {
  for (const entry of readdirSync(dir)) {
    const full = join(dir, entry);
    const st = statSync(full);
    if (st.isDirectory()) {
      if (entry === 'languages') continue; // 保留翻译者历史署名
      walk(full, out);
    } else {
      out.push(full);
    }
  }
  return out;
}

const files = walk(root).filter((f) => /\.(php|js)$/i.test(f));
let changed = 0;
for (const f of files) {
  let c = readFileSync(f, 'utf8');
  const orig = c;
  const base = f.toLowerCase().replace(/\\/g, '/');
  const isMain = base.endsWith('wp-clean-admin.php');

  // 1) 作者署名 @author Sut -> Tanox
  if (c.includes('@author Sut')) {
    c = c.replace(/@author Sut/g, '@author Tanox');
  }
  // 主文件块注释 Author: Sut -> Tanox
  if (isMain && c.includes('Author: Sut')) {
    c = c.replace('Author: Sut', 'Author: Tanox');
  }
  // 2) 主文件版本头双空格修复
  if (isMain && c.includes('Version:  1.8.4')) {
    c = c.replace('Version:  1.8.4', 'Version: 1.8.4');
  }
  // 3) 主文件运行时常量
  if (isMain && c.includes("define( 'WPCA_VERSION', '1.8.4' )")) {
    c = c.replace("define( 'WPCA_VERSION', '1.8.4' )", "define( 'WPCA_VERSION', '1.8.5' )");
  }
  // 4) 文件头 @version 1.8.4 -> 1.8.5（被署名改动的文件随发版同步）
  if (c.includes('@version 1.8.4')) {
    c = c.replace(/@version 1\.8\.4/g, '@version ' + NEW_VERSION);
  }

  if (c !== orig) {
    writeFileSync(f, c, 'utf8');
    changed++;
  }
}
console.log('changed files:', changed);
console.log('done');
