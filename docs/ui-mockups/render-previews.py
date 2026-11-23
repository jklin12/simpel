"""Render preview PNG tiap mockup (lebar 375px, skala 2x) lewat Chrome/Edge + DevTools Protocol.

Pakai:  python docs/ui-mockups/render-previews.py
Perlu: koneksi internet (Tailwind CDN + Google Fonts), Chrome/Edge terpasang, dan paket `websockets`
       (pip install websockets). Set env CHROME_PATH bila Chrome tidak di lokasi bawaan.
Hasil: docs/ui-mockups/previews/*.png
(Tidak memakai --window-size karena Chrome headless punya lebar jendela minimum, sehingga layout tidak 375px.)
"""
import asyncio
import json
import os
import shutil
import subprocess
import sys
import tempfile
import time
import urllib.request
from pathlib import Path

import websockets

HERE = Path(__file__).resolve().parent
OUT = HERE / "previews"
OUT.mkdir(exist_ok=True)
PORT = 9333
WIDTH = 375

CANDIDATES = [
    os.environ.get("CHROME_PATH", ""),
    r"C:\Program Files\Google\Chrome\Application\chrome.exe",
    r"C:\Program Files (x86)\Google\Chrome\Application\chrome.exe",
    r"C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe",
    "/usr/bin/google-chrome",
    "/Applications/Google Chrome.app/Contents/MacOS/Google Chrome",
]
CHROME = next((c for c in CANDIDATES if c and os.path.exists(c)), None)
if not CHROME:
    sys.exit("Chrome/Edge tidak ditemukan. Set env CHROME_PATH.")

# (nama file output, halaman, query, tinggi tetap atau None = halaman penuh)
JOBS = [
    ("beranda", "beranda.html", "", None),
    ("beranda-menu", "beranda.html", "?menu=1", 812),
    ("struktur-organisasi", "struktur-organisasi.html", "", None),
    ("daftar-layanan", "daftar-layanan.html", "", None),
    ("daftar-layanan-sheet", "daftar-layanan.html", "?sheet=1", 812),
    ("download-template", "download-template.html", "", None),
]


class Cdp:
    def __init__(self, ws):
        self.ws, self.i = ws, 0

    async def send(self, method, **params):
        self.i += 1
        await self.ws.send(json.dumps({"id": self.i, "method": method, "params": params}))
        while True:
            msg = json.loads(await self.ws.recv())
            if msg.get("id") == self.i:
                if "error" in msg:
                    raise RuntimeError(f"{method}: {msg['error']}")
                return msg.get("result", {})


async def run(ws_url):
    import base64

    async with websockets.connect(ws_url, max_size=None) as ws:
        c = Cdp(ws)
        await c.send("Page.enable")
        for name, page, query, height in JOBS:
            await c.send("Emulation.setDeviceMetricsOverride", width=WIDTH, height=812, deviceScaleFactor=2, mobile=False)
            await c.send("Page.navigate", url=(HERE / page).as_uri() + query)
            await asyncio.sleep(5)  # tunggu Tailwind CDN, font, dan skrip render
            if height is None:
                r = await c.send("Runtime.evaluate", expression="document.documentElement.scrollHeight", returnByValue=True)
                height = int(r["result"]["value"])
            await c.send("Emulation.setDeviceMetricsOverride", width=WIDTH, height=height, deviceScaleFactor=2, mobile=False)
            await asyncio.sleep(1.2)
            shot = await c.send("Page.captureScreenshot", format="png")
            (OUT / f"{name}.png").write_bytes(base64.b64decode(shot["data"]))
            print(f"{name}: {WIDTH}x{height}px -> previews/{name}.png")


def main():
    profile = tempfile.mkdtemp(prefix="simpel-mock-")
    proc = subprocess.Popen(
        [CHROME, "--headless=new", "--disable-gpu", "--hide-scrollbars", "--no-first-run", f"--remote-debugging-port={PORT}", f"--user-data-dir={profile}", "about:blank"],
        stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL,
    )
    try:
        ws_url = None
        for _ in range(40):
            try:
                targets = json.load(urllib.request.urlopen(f"http://127.0.0.1:{PORT}/json/list"))
                ws_url = next(t["webSocketDebuggerUrl"] for t in targets if t["type"] == "page")
                break
            except Exception:
                time.sleep(0.5)
        if not ws_url:
            sys.exit("Tidak bisa terhubung ke Chrome DevTools.")
        asyncio.run(run(ws_url))
    finally:
        proc.terminate()
        shutil.rmtree(profile, ignore_errors=True)


if __name__ == "__main__":
    main()
