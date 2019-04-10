import socketserver
# for python2
# from BaseHTTPServer import BaseHTTPRequestHandler
# for python3
from http.server import BaseHTTPRequestHandler

import time
import threading

class MyHandler(BaseHTTPRequestHandler):
    def read_data(self):
        print("read data")

    def write_data(self):
        print("Write_data")

    def _set_headers(self):
        self.send_response(200)
        #self.send_header('Content-type', 'application/json')
        self.send_header('Content-type', 'text/html')
        self.end_headers()

    def do_GET(self):
        if self.path == '/readData':
            read_data()
        elif self.path == '/writeData':
            write_data()
        else:
            BaseHTTPRequestHandler.do_GET()
        self._set_headers()

    def do_POST(self):
        # '''Reads post request body'''
        self._set_headers()
        content_len = int(self.headers.getheader('content-length', 0))
        post_body = self.rfile.read(content_len)
        self.wfile.write("received post request:<br>{}".format(post_body))

    def do_PUT(self):
        self.do_POST()

class NRWeb:
    loop = True

    def stopWeb(self):
        NRWeb.loop=False

    def webLoop(self):
        server_thread = threading.Thread(target=self.httpd.serve_forever)
        # Exit the server thread when the main thread terminates
        server_thread.daemon = True
        server_thread.start()
        # as using threads for clients, main thread only monitorizes end of loop, so just sleep and wait
        while NRWeb.loop == True:
            time.sleep(5) # check for end every 5 seconds,
        self.httpd.shutdown()
        self.httpd.server_close()

        # while NRWen.loop == True

    def __init__(self):
        handler=MyHandler
        self.httpd = socketserver.TCPServer(("", 8000), handler)
        self.httpd.server_name = "localhost"
        self.httpd.server_port = 8000
